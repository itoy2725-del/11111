<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Import;
use App\Models\FraudType;
use App\Models\LossRange;
use App\Models\WalletType;
use App\Models\LeadHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportService
{
    public function validateHeaders(array $headers): array
    {
        $normalizedHeaders = array_map(function($h) {
            $h = preg_replace('/\x{FEFF}/u', '', $h);
            return strtolower(trim(str_replace([' ', '-'], '_', $h)));
        }, $headers);

        // Check for telephone column in Turkish or English
        $phoneKeys = ['telefon_numarası', 'telefon', 'phone_number', 'phone', 'mobile'];
        $hasPhone = false;
        foreach ($phoneKeys as $key) {
            if (in_array($key, $normalizedHeaders)) {
                $hasPhone = true;
                break;
            }
        }

        if (!$hasPhone) {
            return [
                'valid' => false,
                'missing' => ['Telefon Numarası (telefon_numarası / phone_number)']
            ];
        }

        return [
            'valid' => true,
            'missing' => []
        ];
    }

    public function parseCSV(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle, 10000, ',');
            if (!$headers) {
                fclose($handle);
                return [];
            }
            
            // Normalize headers: remove BOM, trim, lowercase, replace spaces/hyphens
            $headers = array_map(function($h) {
                $h = preg_replace('/\x{FEFF}/u', '', $h);
                return strtolower(trim(str_replace([' ', '-'], '_', $h)));
            }, $headers);
            
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                if (count($headers) !== count($data)) {
                    continue;
                }
                
                $row = array_combine($headers, $data);
                
                // Flexible phone extraction & normalization: p:+905399271922 → +905399271922
                $phoneVal = $row['telefon_numarası'] ?? $row['phone_number'] ?? $row['telefon'] ?? $row['phone'] ?? null;
                if ($phoneVal) {
                    $row['normalized_phone'] = preg_replace('/^p:/', '', trim($phoneVal));
                }
                
                // Flexible email extraction
                $emailVal = $row['e-posta'] ?? $row['email'] ?? null;
                if ($emailVal) {
                    $row['normalized_email'] = strtolower(trim($emailVal));
                }
                
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }

    public function checkDuplicates(array $rows): array
    {
        $new = [];
        $duplicates = [];
        $errors = [];
        
        $existingPhones = Lead::pluck('id', 'telefon')->toArray();
        
        foreach ($rows as $index => $row) {
            $phone = $row['normalized_phone'] ?? null;
            if (!$phone) {
                $errors[] = "Satır " . ($index + 2) . ": Telefon numarası bulunamadı";
                continue;
            }
            
            if (isset($existingPhones[$phone])) {
                $duplicates[] = [
                    'row' => $row,
                    'existing_id' => $existingPhones[$phone]
                ];
            } else {
                $new[] = $row;
                $existingPhones[$phone] = 0;
            }
        }
        
        return [
            'new' => $new,
            'duplicates' => $duplicates,
            'errors' => $errors
        ];
    }

    public function processImport(array $rows, string $duplicateAction, int $userId, string $fileName = null): Import
    {
        return DB::transaction(function () use ($rows, $duplicateAction, $userId, $fileName) {
            $basarili = 0;
            $mukerrer = 0;
            $hataSayisi = 0;
            
            $fraudTypes = FraudType::pluck('id', 'isim')->toArray();
            $lossRanges = LossRange::pluck('id', 'isim')->toArray();
            $walletTypes = WalletType::pluck('id', 'isim')->toArray();
            
            foreach ($rows as $row) {
                $phone = $row['normalized_phone'] ?? null;
                if (!$phone) {
                    $hataSayisi++;
                    continue;
                }

                try {
                    $data = [
                        'meta_lead_id' => $row['id'] ?? null,
                        'created_time' => isset($row['created_time']) ? Carbon::parse($row['created_time']) : null,
                        'ad_id' => $row['ad_id'] ?? null,
                        'ad_name' => $row['ad_name'] ?? null,
                        'adset_id' => $row['adset_id'] ?? null,
                        'adset_name' => $row['adset_name'] ?? null,
                        'campaign_id' => $row['campaign_id'] ?? null,
                        'campaign_name' => $row['campaign_name'] ?? null,
                        'form_id' => $row['form_id'] ?? null,
                        'form_name' => $row['form_name'] ?? null,
                        'is_organic' => isset($row['is_organic']) && strtolower($row['is_organic']) === 'true',
                        'platform' => $row['platform'] ?? null,
                        'telefon' => $phone,
                        'email' => $row['normalized_email'] ?? null,
                        'sikayet_durumu' => $row['polise_siber_suç_yetkililerine_veya_mali_düzenleyicilere_bir_ihbar_şikayet_yaptınız_mı'] ?? null,
                        'ek_guvenlik_hizmeti' => $row['gelecekteki_kayıpları_önlemek_icin_ek_güvenlik_hizmetleriyle_ilgilenir_misiniz'] ?? null,
                        'toplam_kripto' => $row['tüm_cüzdanlarınızda_şu_andan_ne_kadar_kriptonuz_bulunuyor'] ?? null,
                        'status_id' => 1,
                    ];
                    
                    // Fraud type lookup
                    $fraudKey = $row['durumunuz_en_iyisi_hangisi_tanımlıyor'] ?? null;
                    if ($fraudKey && isset($fraudTypes[$fraudKey])) {
                        $data['fraud_type_id'] = $fraudTypes[$fraudKey];
                    }
                    
                    // Loss range lookup
                    $lossKey = $row['ne_kadar_kripto_kaybedildi'] ?? null;
                    if ($lossKey && isset($lossRanges[$lossKey])) {
                        $data['loss_range_id'] = $lossRanges[$lossKey];
                    }
                    
                    // Wallet type lookup
                    $walletKey = $row['fonlarınızı_göndermek_için_kullandığınız_cüzdanı_seçin'] ?? null;
                    if ($walletKey && isset($walletTypes[$walletKey])) {
                        $data['wallet_type_id'] = $walletTypes[$walletKey];
                    }

                    $existing = Lead::where('telefon', $phone)->first();

                    if ($existing) {
                        if ($duplicateAction === 'update') {
                            $existing->update($data);
                            LeadHistory::create([
                                'lead_id' => $existing->id,
                                'islem' => 'CSV Import ile güncellendi',
                                'yapan_kullanici' => $userId,
                            ]);
                            $basarili++;
                        } elseif ($duplicateAction === 'create_new') {
                            $lead = Lead::create($data);
                            LeadHistory::create([
                                'lead_id' => $lead->id,
                                'islem' => 'CSV Import ile oluşturuldu (mükerrer)',
                                'yapan_kullanici' => $userId,
                            ]);
                            $basarili++;
                        } else {
                            $mukerrer++;
                        }
                    } else {
                        $lead = Lead::create($data);
                        LeadHistory::create([
                            'lead_id' => $lead->id,
                            'islem' => 'CSV Import ile oluşturuldu',
                            'yapan_kullanici' => $userId,
                        ]);
                        $basarili++;
                    }
                } catch (\Exception $e) {
                    $hataSayisi++;
                }
            }
            
            $import = Import::create([
                'dosya_adi' => $fileName ?: ('import_' . date('Y-m-d_H-i-s') . '.csv'),
                'toplam_kayit' => count($rows),
                'basarili' => $basarili,
                'mukerrer' => $mukerrer,
                'hata_sayisi' => $hataSayisi,
                'import_detay_json' => [
                    'duplicate_action' => $duplicateAction,
                    'import_date' => now()->toDateTimeString(),
                ],
                'yukleyen' => $userId,
            ]);
            
            AuditService::logStatic(
                'CSV Import tamamlandı',
                'Import',
                $import->id,
                null,
                "Toplam: " . count($rows) . ", Başarılı: {$basarili}, Mükerrer: {$mukerrer}, Hata: {$hataSayisi}"
            );
            
            return $import;
        });
    }
}

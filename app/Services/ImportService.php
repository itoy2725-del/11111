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
        // Flexible validation: parseCSV intelligently auto-detects phone & email columns
        return [
            'valid' => true,
            'missing' => []
        ];
    }

    public function parseCSV(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $rawHeaders = fgetcsv($handle, 10000, ',');
            if (!$rawHeaders) {
                fclose($handle);
                return [];
            }
            
            // Normalize headers: remove BOM, trim, lowercase, replace spaces/hyphens
            $headers = array_map(function($h) {
                $h = preg_replace('/\x{FEFF}/u', '', $h);
                return strtolower(trim(str_replace([' ', '-'], '_', $h)));
            }, $rawHeaders);

            // Intelligent phone column detection
            $phoneHeader = null;
            foreach ($headers as $h) {
                if (
                    str_contains($h, 'telefon') || 
                    str_contains($h, 'phone') || 
                    str_contains($h, 'mobile') || 
                    str_contains($h, 'tel') || 
                    str_contains($h, 'numar')
                ) {
                    $phoneHeader = $h;
                    break;
                }
            }

            // Intelligent email column detection
            $emailHeader = null;
            foreach ($headers as $h) {
                if (str_contains($h, 'email') || str_contains($h, 'posta') || str_contains($h, 'mail')) {
                    $emailHeader = $h;
                    break;
                }
            }
            
            $rawRows = [];
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                if (count($headers) !== count($data)) {
                    continue;
                }
                $rawRows[] = array_combine($headers, $data);
            }
            fclose($handle);

            // Fallback: If no phone header matched keywords, inspect first row data for phone patterns
            if (!$phoneHeader && !empty($rawRows)) {
                $firstRow = $rawRows[0];
                foreach ($firstRow as $col => $val) {
                    $cleanVal = preg_replace('/[^\d]/', '', (string)$val);
                    if (strlen($cleanVal) >= 7 && (str_starts_with($cleanVal, '90') || str_starts_with($cleanVal, '05') || str_starts_with($cleanVal, '5'))) {
                        $phoneHeader = $col;
                        break;
                    }
                }
            }

            // Build final rows
            foreach ($rawRows as $row) {
                $phoneVal = $phoneHeader ? ($row[$phoneHeader] ?? null) : null;
                if (!$phoneVal) {
                    // Last resort: search row for any phone-like string
                    foreach ($row as $v) {
                        $c = preg_replace('/[^\d]/', '', (string)$v);
                        if (strlen($c) >= 7 && (str_starts_with($c, '90') || str_starts_with($c, '05') || str_starts_with($c, '5'))) {
                            $phoneVal = $v;
                            break;
                        }
                    }
                }

                if ($phoneVal) {
                    $row['normalized_phone'] = preg_replace('/^p:/', '', trim($phoneVal));
                }

                $emailVal = $emailHeader ? ($row[$emailHeader] ?? null) : null;
                if ($emailVal) {
                    $row['normalized_email'] = strtolower(trim($emailVal));
                }

                $rows[] = $row;
            }
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
                $errors[] = "Satır " . ($index + 2) . ": Telefon numarası tespit edilemedi";
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

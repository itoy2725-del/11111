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
        return [
            'valid' => true,
            'missing' => []
        ];
    }

    public function parseCSV(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            
            // Read first line to auto-detect delimiter
            $firstLine = fgets($handle);
            rewind($handle);

            $delimiter = ',';
            if (substr_count($firstLine, "\t") >= 2) {
                $delimiter = "\t";
            } elseif (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            }

            $rawHeaders = fgetcsv($handle, 10000, $delimiter);
            if (!$rawHeaders) {
                fclose($handle);
                return [];
            }
            
            // Strip invisible Meta Ads zero-width unicode characters (\x{034F}, \x{200B}, \x{FEFF}, etc.)
            $headers = array_map(function($h) {
                $h = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{200B}-\x{200D}\x{FEFF}\x{0300}-\x{036F}]/u', '', $h);
                return strtolower(trim(str_replace([' ', '-'], '_', $h)));
            }, $rawHeaders);

            // Intelligent phone column header finder
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

            // Intelligent email column header finder
            $emailHeader = null;
            foreach ($headers as $h) {
                if (str_contains($h, 'email') || str_contains($h, 'posta') || str_contains($h, 'mail')) {
                    $emailHeader = $h;
                    break;
                }
            }

            // Intelligent name column header finder
            $nameHeader = null;
            foreach ($headers as $h) {
                if (str_contains($h, 'full_name') || str_contains($h, 'ad_soyad') || str_contains($h, 'name') || str_contains($h, 'isim')) {
                    $nameHeader = $h;
                    break;
                }
            }

            // Intelligent campaign column header finder
            $campaignHeader = null;
            foreach ($headers as $h) {
                if (str_contains($h, 'campaign') || str_contains($h, 'kampanya')) {
                    $campaignHeader = $h;
                    break;
                }
            }
            
            $rawRows = [];
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
                if (count($headers) !== count($data)) {
                    continue;
                }
                $rawRows[] = array_combine($headers, $data);
            }
            fclose($handle);

            // Process each row
            foreach ($rawRows as $row) {
                $phoneVal = null;

                // 1. Try identified phone header column
                if ($phoneHeader && !empty($row[$phoneHeader])) {
                    $phoneVal = $row[$phoneHeader];
                }

                // 2. Direct search across row cells for Meta phone pattern (p:+90..., +90..., 05..., 5...)
                if (!$phoneVal) {
                    foreach ($row as $v) {
                        if (is_string($v) && (str_contains($v, 'p:+') || str_contains($v, '+90') || str_contains($v, '05'))) {
                            $phoneVal = $v;
                            break;
                        }
                        $cleanDigits = preg_replace('/[^\d]/', '', (string)$v);
                        if (strlen($cleanDigits) >= 10 && (str_starts_with($cleanDigits, '90') || str_starts_with($cleanDigits, '05') || str_starts_with($cleanDigits, '5'))) {
                            $phoneVal = $v;
                            break;
                        }
                    }
                }

                // Normalize phone
                if ($phoneVal) {
                    $phoneVal = preg_replace('/^p:/i', '', trim($phoneVal));
                    $row['normalized_phone'] = $phoneVal;
                }

                // Email extraction
                $emailVal = null;
                if ($emailHeader && !empty($row[$emailHeader])) {
                    $emailVal = $row[$emailHeader];
                }
                if (!$emailVal) {
                    foreach ($row as $v) {
                        if (is_string($v) && str_contains($v, '@') && str_contains($v, '.')) {
                            $emailVal = trim($v);
                            break;
                        }
                    }
                }
                if ($emailVal) {
                    $row['normalized_email'] = strtolower(trim($emailVal));
                }

                // Name extraction
                if ($nameHeader && !empty($row[$nameHeader])) {
                    $row['ad_soyad'] = trim($row[$nameHeader]);
                } else {
                    // Use email username or lead ID as name fallback
                    if (!empty($row['normalized_email'])) {
                        $emailUser = explode('@', $row['normalized_email'])[0];
                        $row['ad_soyad'] = ucfirst(str_replace(['.', '_', '-'], ' ', $emailUser));
                    } elseif (!empty($row['id'])) {
                        $row['ad_soyad'] = 'Lead ' . $row['id'];
                    } else {
                        $row['ad_soyad'] = 'Meta Lead';
                    }
                }

                // Campaign extraction
                if ($campaignHeader && !empty($row[$campaignHeader])) {
                    $row['campaign_name'] = trim($row[$campaignHeader]);
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
                    $createdTime = null;
                    if (!empty($row['created_time'])) {
                        try {
                            $createdTime = Carbon::parse($row['created_time']);
                        } catch (\Throwable $e) {
                            $createdTime = null;
                        }
                    }

                    $data = [
                        'ad_soyad' => $row['ad_soyad'] ?? 'Meta Lead',
                        'meta_lead_id' => $row['id'] ?? null,
                        'created_time' => $createdTime,
                        'ad_id' => $row['ad_id'] ?? null,
                        'ad_name' => $row['ad_name'] ?? null,
                        'adset_id' => $row['adset_id'] ?? null,
                        'adset_name' => $row['adset_name'] ?? null,
                        'campaign_id' => $row['campaign_id'] ?? null,
                        'campaign_name' => $row['campaign_name'] ?? $row['campaign'] ?? null,
                        'form_id' => $row['form_id'] ?? null,
                        'form_name' => $row['form_name'] ?? null,
                        'is_organic' => isset($row['is_organic']) && strtolower((string)$row['is_organic']) === 'true',
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

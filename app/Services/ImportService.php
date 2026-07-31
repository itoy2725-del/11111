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

    public static function cleanMetaText(?string $str): string
    {
        if (empty($str)) return '';

        // Strip Meta Ads zero-width unicode grapheme joiners (\x{034F}), tag characters (\x{E0000}-\x{E007F}), BOM, etc.
        $str = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{0300}-\x{036F}\x{034F}\x{200B}-\x{200D}\x{FEFF}\x{E0000}-\x{E007F}]/u', '', $str);
        
        // Fix distorted character pattern e.g., dOoOlOaOrO -> dolar
        $str = preg_replace('/([a-zA-Z0-9çğıöşüÇĞİÖŞÜ])O(?=[a-zA-Z0-9çğıöşüÇĞİÖŞÜ])/u', '$1', $str);
        
        // Normalize common Turkish typos from raw Meta exports
        $trReplacements = [
            'dier' => 'diğer',
            'hay1r' => 'hayır',
            'yanl1' => 'yanlış',
            'fOoOrOeOxO' => 'forex',
            'dOoOlOaOrO' => 'dolar',
            'tOrOuOsOtO' => 'trust',
            'wOaOlOlOeOtO' => 'wallet',
            'mOeOtOaOmOaOsOkO' => 'metamask',
            'bOoOrOsOaO' => 'borsa',
            'rOuOgO' => 'rug',
            'pOuOlOlO' => 'pull',
            '_' => ' ',
        ];

        foreach ($trReplacements as $search => $replace) {
            $str = str_ireplace($search, $replace, $str);
        }

        return trim(preg_replace('/\s+/', ' ', $str));
    }

    public function parseCSV(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            
            // Auto-detect CSV delimiter
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
            
            // Normalize headers
            $headers = array_map(function($h) {
                $h = self::cleanMetaText($h);
                return strtolower(trim(str_replace([' ', '-'], '_', $h)));
            }, $rawHeaders);

            $rawRows = [];
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
                if (count($data) === 0 || (count($data) === 1 && empty($data[0]))) {
                    continue;
                }
                $rawRows[] = $data;
            }
            fclose($handle);

            foreach ($rawRows as $data) {
                $row = [];
                
                // Map by header keys
                foreach ($data as $idx => $val) {
                    $cleanVal = self::cleanMetaText($val);
                    if (isset($headers[$idx])) {
                        $row[$headers[$idx]] = $cleanVal;
                    }
                    $row['col_' . $idx] = $cleanVal;
                }

                // Phone Extraction (by keyword or index 18 or string pattern)
                $phoneVal = null;
                foreach ($row as $k => $v) {
                    if (str_contains($k, 'telefon') || str_contains($k, 'phone')) {
                        $phoneVal = $v;
                        break;
                    }
                }
                if (!$phoneVal && isset($data[18])) {
                    $phoneVal = self::cleanMetaText($data[18]);
                }
                if (!$phoneVal) {
                    foreach ($data as $v) {
                        $vClean = self::cleanMetaText($v);
                        if (str_contains($vClean, 'p:+') || str_contains($vClean, '+90')) {
                            $phoneVal = $vClean;
                            break;
                        }
                        $cleanDigits = preg_replace('/[^\d]/', '', $vClean);
                        if (strlen($cleanDigits) >= 10 && (str_starts_with($cleanDigits, '90') || str_starts_with($cleanDigits, '05') || str_starts_with($cleanDigits, '5'))) {
                            $phoneVal = $vClean;
                            break;
                        }
                    }
                }

                // Filter out empty trailing/header rows without a valid phone number
                if (!$phoneVal) {
                    continue;
                }

                // Clean phone number (strip 'p:' prefix)
                $phoneVal = preg_replace('/^p:/i', '', trim($phoneVal));
                $row['normalized_phone'] = $phoneVal;

                // Email Extraction
                $emailVal = null;
                foreach ($row as $k => $v) {
                    if (str_contains($k, 'posta') || str_contains($k, 'email') || str_contains($k, 'mail')) {
                        $emailVal = $v;
                        break;
                    }
                }
                if (!$emailVal && isset($data[19])) {
                    $emailVal = self::cleanMetaText($data[19]);
                }
                if (!$emailVal) {
                    foreach ($data as $v) {
                        $vClean = self::cleanMetaText($v);
                        if (str_contains($vClean, '@') && str_contains($vClean, '.')) {
                            $emailVal = $vClean;
                            break;
                        }
                    }
                }
                if ($emailVal) {
                    $row['normalized_email'] = strtolower(trim($emailVal));
                }

                // Clean Ad Name & Campaign Name
                $adName = self::cleanMetaText($data[3] ?? $row['ad_name'] ?? '');
                $adName = trim(str_replace(['"', 'ag:'], '', $adName));
                $row['clean_ad_name'] = $adName ?: 'New Leads Ad';

                $campaignName = self::cleanMetaText($data[7] ?? $row['campaign_name'] ?? '');
                $campaignName = trim(str_replace(['"', 'c:'], '', $campaignName));
                $row['clean_campaign_name'] = $campaignName ?: 'New Leads Campaign';

                // Extract Form Answers by Index
                $row['form_fraud'] = self::cleanMetaText($data[12] ?? $row['durumunuzu_en_iyi_hangisi_tanimliyor'] ?? '-');
                $row['form_loss'] = self::cleanMetaText($data[13] ?? $row['ne_kadar_kripto_kaybedildi'] ?? '-');
                $row['form_wallet'] = self::cleanMetaText($data[14] ?? $row['cuzdan'] ?? '-');
                $row['form_complaint'] = self::cleanMetaText($data[15] ?? $row['sikayet'] ?? '-');
                $row['form_security'] = self::cleanMetaText($data[16] ?? $row['ek_guvenlik'] ?? '-');
                $row['form_crypto'] = self::cleanMetaText($data[17] ?? $row['toplam_kripto'] ?? '-');

                // Derive Ad Soyad
                if (!empty($row['normalized_email'])) {
                    $emailUser = explode('@', $row['normalized_email'])[0];
                    $row['ad_soyad'] = ucfirst(str_replace(['.', '_', '-'], ' ', $emailUser));
                } else {
                    $row['ad_soyad'] = 'Meta Lead ' . ($row['col_0'] ?? '');
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
                    $createdTimeVal = $row['col_1'] ?? $row['created_time'] ?? null;
                    $createdTime = null;
                    if (!empty($createdTimeVal)) {
                        try {
                            $createdTime = Carbon::parse($createdTimeVal);
                        } catch (\Throwable $e) {
                            $createdTime = null;
                        }
                    }

                    $data = [
                        'ad_soyad' => $row['ad_soyad'] ?? 'Meta Lead',
                        'meta_lead_id' => $row['col_0'] ?? $row['id'] ?? null,
                        'created_time' => $createdTime,
                        'ad_id' => $row['col_2'] ?? null,
                        'ad_name' => $row['clean_ad_name'] ?? null,
                        'adset_id' => $row['col_4'] ?? null,
                        'adset_name' => $row['col_5'] ?? null,
                        'campaign_id' => $row['col_6'] ?? null,
                        'campaign_name' => $row['clean_campaign_name'] ?? null,
                        'form_id' => $row['col_8'] ?? null,
                        'form_name' => $row['col_9'] ?? null,
                        'is_organic' => isset($row['col_10']) && strtolower((string)$row['col_10']) === 'true',
                        'platform' => $row['col_11'] ?? $row['platform'] ?? 'fb',
                        'telefon' => $phone,
                        'email' => $row['normalized_email'] ?? null,
                        'sikayet_durumu' => $row['form_complaint'] ?? null,
                        'ek_guvenlik_hizmeti' => $row['form_security'] ?? null,
                        'toplam_kripto' => $row['form_crypto'] ?? null,
                        'status_id' => 1,
                    ];
                    
                    // Fraud type lookup
                    $fraudKey = $row['form_fraud'] ?? null;
                    if ($fraudKey && isset($fraudTypes[$fraudKey])) {
                        $data['fraud_type_id'] = $fraudTypes[$fraudKey];
                    }
                    
                    // Loss range lookup
                    $lossKey = $row['form_loss'] ?? null;
                    if ($lossKey && isset($lossRanges[$lossKey])) {
                        $data['loss_range_id'] = $lossRanges[$lossKey];
                    }
                    
                    // Wallet type lookup
                    $walletKey = $row['form_wallet'] ?? null;
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

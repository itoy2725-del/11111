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

    public static function formatCryptoAmount(?string $str): string
    {
        if (empty($str) || $str === '-' || $str === 'Belirtilmedi') return 'Belirtilmedi';

        // Strip invisible unicode grapheme joiners (\x{034F})
        $str = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{0300}-\x{036F}\x{034F}\x{200B}-\x{200D}\x{FEFF}\x{E0000}-\x{E007F}]/u', '', $str);
        $lower = strtolower($str);

        if (str_contains($lower, '1000') && (str_contains($lower, 'az') || str_contains($lower, 'dan'))) {
            return '1k$\'dan Az';
        }
        if ((str_contains($lower, '1.000') && str_contains($lower, '10.000')) || (str_contains($lower, '1000') && str_contains($lower, '10000'))) {
            return '1 - 10k$';
        }
        if ((str_contains($lower, '10.000') && str_contains($lower, '50.000')) || (str_contains($lower, '10000') && str_contains($lower, '50000'))) {
            return '10 - 50k$';
        }
        if (str_contains($lower, '50.000') || str_contains($lower, '50000') || str_contains($lower, 'üzeri') || str_contains($lower, 'uzeri')) {
            return '50k$+';
        }

        return self::cleanMetaText($str);
    }

    public static function cleanMetaText(?string $str): string
    {
        if (empty($str)) return '';

        // Strip Meta Ads zero-width unicode grapheme joiners (\x{034F}), tag characters, BOM
        $str = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{0300}-\x{036F}\x{034F}\x{200B}-\x{200D}\x{FEFF}\x{E0000}-\x{E007F}]/u', '', $str);

        // Remove underscores and normalize spaces
        $cleanStr = trim(str_replace('_', ' ', $str));
        $cleanStr = preg_replace('/\s+/', ' ', $cleanStr);
        $lower = strtolower($cleanStr);

        // Map Fraud Types
        if (str_contains($lower, 'forex')) {
            return 'Forex Dolandırıcılığı';
        }
        if (str_contains($lower, 'borsa') || str_contains($lower, 'rug pull') || str_contains($lower, 'rugpull')) {
            return 'Borsa Dolandırıcılığı / Rug Pull';
        }
        if (str_contains($lower, 'yanlış') || str_contains($lower, 'yanlis') || str_contains($lower, 'adrese')) {
            return 'Yanlış Adrese Gönderildi';
        }
        if (str_contains($lower, 'metamask')) {
            return 'MetaMask';
        }
        if (str_contains($lower, 'trust')) {
            return 'Trust Wallet';
        }
        if (str_contains($lower, 'dier') || str_contains($lower, 'diger') || str_contains($lower, 'diğer')) {
            return 'Diğer';
        }

        return mb_convert_case($cleanStr, MB_CASE_TITLE, "UTF-8");
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
                
                // Map raw fields
                foreach ($data as $idx => $val) {
                    $cleanVal = self::cleanMetaText($val);
                    if (isset($headers[$idx])) {
                        $row[$headers[$idx]] = $cleanVal;
                    }
                    $row['col_' . $idx] = $cleanVal;
                }

                // Phone Extraction
                $phoneVal = null;
                if (isset($data[18])) {
                    $phoneVal = trim($data[18]);
                }
                if (!$phoneVal) {
                    foreach ($data as $v) {
                        if (str_contains($v, 'p:+') || str_contains($v, '+90')) {
                            $phoneVal = $v;
                            break;
                        }
                        $cleanDigits = preg_replace('/[^\d]/', '', $v);
                        if (strlen($cleanDigits) >= 10 && (str_starts_with($cleanDigits, '90') || str_starts_with($cleanDigits, '05') || str_starts_with($cleanDigits, '5'))) {
                            $phoneVal = $v;
                            break;
                        }
                    }
                }

                if (!$phoneVal) {
                    continue;
                }

                // Clean phone number (strip 'p:' prefix)
                $phoneVal = preg_replace('/^p:/i', '', trim($phoneVal));
                $row['normalized_phone'] = $phoneVal;

                // RAW Email Extraction
                $emailVal = null;
                foreach ($data as $v) {
                    $vRaw = trim($v);
                    if (str_contains($vRaw, '@') && (str_contains($vRaw, '.com') || str_contains($vRaw, '.net') || str_contains($vRaw, '.org') || str_contains($vRaw, '.cm') || str_contains($vRaw, '.'))) {
                        $emailVal = $vRaw;
                        break;
                    }
                }
                if ($emailVal) {
                    $emailVal = str_replace(['.cm', 'htmail'], ['.com', 'hotmail'], strtolower($emailVal));
                    $row['normalized_email'] = $emailVal;
                }

                // Clean Ad Name & Campaign Name
                $adName = self::cleanMetaText($data[3] ?? $row['ad_name'] ?? '');
                $adName = trim(str_replace(['"', 'ag:'], '', $adName));
                $row['clean_ad_name'] = $adName ?: 'New Leads Ad';

                $campaignName = self::cleanMetaText($data[7] ?? $row['campaign_name'] ?? '');
                $campaignName = trim(str_replace(['"', 'c:'], '', $campaignName));
                $row['clean_campaign_name'] = $campaignName ?: 'New Leads Campaign';

                // --- UNICODE CLEANED FORM ANSWERS (Cell 12: Fraud, Cell 13: Loss, Cell 14: Wallet) ---
                $row['form_fraud'] = self::cleanMetaText($data[12] ?? '');
                $row['form_loss'] = self::formatCryptoAmount($data[13] ?? '');
                $row['form_wallet'] = self::cleanMetaText($data[14] ?? '');
                $row['form_complaint'] = self::cleanMetaText($data[15] ?? 'Hayır');
                $row['form_security'] = self::cleanMetaText($data[16] ?? 'Evet');
                $row['form_crypto'] = self::formatCryptoAmount($data[17] ?? $data[13] ?? '');

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
                        'sikayet_durumu' => $row['form_complaint'] ?? 'Hayır',
                        'ek_guvenlik_hizmeti' => $row['form_security'] ?? 'Evet',
                        'toplam_kripto' => $row['form_crypto'] ?? null,
                        'status_id' => 1,
                    ];
                    
                    // Fraud Type Lookup
                    $fraudName = $row['form_fraud'] ?? 'Diğer';
                    if (str_contains(strtolower($fraudName), 'forex')) {
                        $data['fraud_type_id'] = 1; // Forex Dolandırıcılığı
                    } elseif (str_contains(strtolower($fraudName), 'borsa') || str_contains(strtolower($fraudName), 'rug')) {
                        $data['fraud_type_id'] = 2; // Borsa Dolandırıcılığı / Rug Pull
                    } elseif (str_contains(strtolower($fraudName), 'yanlış') || str_contains(strtolower($fraudName), 'adrese')) {
                        $data['fraud_type_id'] = 3; // Yanlış Adrese Gönderildi
                    } else {
                        $data['fraud_type_id'] = 5; // Diğer
                    }
                    
                    // Loss Range Lookup
                    $lossName = $row['form_loss'] ?? 'Belirtilmedi';
                    if (str_contains($lossName, '1k$\'dan Az')) {
                        $data['loss_range_id'] = 1; // 0 - 1.000 USD
                    } elseif (str_contains($lossName, '1 - 10k$')) {
                        $data['loss_range_id'] = 2; // 1.000 - 5.000 USD
                    } elseif (str_contains($lossName, '10 - 50k$')) {
                        $data['loss_range_id'] = 4; // 10.000 - 50.000 USD
                    } elseif (str_contains($lossName, '50k$+')) {
                        $data['loss_range_id'] = 5; // 50.000 - 100.000 USD
                    }

                    // Wallet Type Lookup
                    $walletName = $row['form_wallet'] ?? 'Diğer';
                    $walletLower = strtolower($walletName);
                    if (str_contains($walletLower, 'metamask')) {
                        $data['wallet_type_id'] = 1; // MetaMask
                    } elseif (str_contains($walletLower, 'trust')) {
                        $data['wallet_type_id'] = 2; // Trust Wallet
                    } elseif (str_contains($walletLower, 'binance')) {
                        $data['wallet_type_id'] = 3; // Binance
                    } elseif (str_contains($walletLower, 'coinbase')) {
                        $data['wallet_type_id'] = 4; // Coinbase
                    } elseif (str_contains($walletLower, 'ledger')) {
                        $data['wallet_type_id'] = 5; // Ledger
                    } else {
                        $data['wallet_type_id'] = 6; // Diğer
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

<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(string $islem, ?string $kayitTipi = null, ?int $kayitId = null, ?string $eskiDeger = null, ?string $yeniDeger = null): void
    {
        self::logStatic($islem, $kayitTipi, $kayitId, $eskiDeger, $yeniDeger);
    }

    public static function logStatic(string $islem, ?string $kayitTipi = null, ?int $kayitId = null, ?string $eskiDeger = null, ?string $yeniDeger = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'islem' => $islem,
            'kayit_tipi' => $kayitTipi,
            'kayit_id' => $kayitId,
            'eski_deger' => $eskiDeger,
            'yeni_deger' => $yeniDeger,
            'ip' => request()->ip(),
        ]);
    }
}

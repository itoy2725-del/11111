<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'dosya_adi',
        'toplam_kayit',
        'basarili',
        'mukerrer',
        'hata_sayisi',
        'import_detay_json',
        'yukleyen',
    ];

    protected $casts = [
        'import_detay_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'yukleyen');
    }
}

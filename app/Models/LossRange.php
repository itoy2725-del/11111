<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LossRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'isim',
        'min_deger',
        'max_deger',
        'sira',
    ];

    protected $casts = [
        'min_deger' => 'decimal:2',
        'max_deger' => 'decimal:2',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}

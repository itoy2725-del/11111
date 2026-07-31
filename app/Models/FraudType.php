<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FraudType extends Model
{
    use HasFactory;

    protected $fillable = [
        'isim',
        'sira',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}

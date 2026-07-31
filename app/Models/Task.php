<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'operator_id',
        'baslik',
        'aciklama',
        'tarih',
        'durum',
    ];

    protected $casts = [
        'tarih' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function scopeBekliyor($query)
    {
        return $query->where('durum', 'bekliyor');
    }

    public function scopeBugun($query)
    {
        return $query->whereDate('tarih', Carbon::today());
    }

    public function scopeByOperator($query, $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }
}

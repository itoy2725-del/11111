<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meta_lead_id',
        'created_time',
        'ad_id',
        'ad_name',
        'adset_id',
        'adset_name',
        'campaign_id',
        'campaign_name',
        'form_id',
        'form_name',
        'is_organic',
        'platform',
        'ad_soyad',
        'telefon',
        'email',
        'fraud_type_id',
        'loss_range_id',
        'wallet_type_id',
        'sikayet_durumu',
        'ek_guvenlik_hizmeti',
        'toplam_kripto',
        'status_id',
        'atanan_operator_id',
        'sonraki_arama_tarihi',
        'operator_notu',
    ];

    protected $casts = [
        'created_time' => 'datetime',
        'is_organic' => 'boolean',
        'sonraki_arama_tarihi' => 'datetime',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atanan_operator_id');
    }

    public function fraudType(): BelongsTo
    {
        return $this->belongsTo(FraudType::class);
    }

    public function lossRange(): BelongsTo
    {
        return $this->belongsTo(LossRange::class);
    }

    public function walletType(): BelongsTo
    {
        return $this->belongsTo(WalletType::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LeadHistory::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('ad_soyad', 'like', "%{$search}%")
              ->orWhere('telefon', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    public function scopeByOperator($query, $operatorId)
    {
        return $query->where('atanan_operator_id', $operatorId);
    }

    public function scopeByCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }
}

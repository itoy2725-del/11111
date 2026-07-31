<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'isim',
        'email',
        'password',
        'rol',
        'aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'aktif' => 'boolean',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'atanan_operator_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'operator_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeOperators($query)
    {
        return $query->where('rol', 'operator');
    }

    public function scopeAdmins($query)
    {
        return $query->where('rol', 'super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'super_admin';
    }

    public function isOperator(): bool
    {
        return $this->rol === 'operator';
    }
}

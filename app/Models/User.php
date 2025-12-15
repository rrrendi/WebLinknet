<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ROLE METHODS
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function canCreateUser(): bool
    {
        return $this->isAdmin();
    }

    public function canDeleteActivity($activityUserId): bool
    {
        return $this->id === $activityUserId;
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // RELATIONSHIPS
    public function igiScans()
    {
        return $this->hasMany(\App\Models\IgiDetail::class, 'scan_by');
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\ActivityLog::class, 'user_id');
    }
}
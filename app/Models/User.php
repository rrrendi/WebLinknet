<?php
// app/Models/User.php

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

    // ========================================
    // ROLE CHECKER METHODS
    // ========================================
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // ⭐ TAMBAH INI
    public function isTamu(): bool
    {
        return $this->role === 'tamu';
    }

    public function canCreateUser(): bool
    {
        return $this->isAdmin();
    }

    public function canDeleteActivity($activityUserId): bool
    {
        return $this->id === $activityUserId;
    }

    // ⭐ TAMBAH INI - Cek apakah bisa akses modul tertentu
    public function canAccessIgi(): bool
    {
        return in_array($this->role, ['admin', 'user']);
    }

    public function canAccessDownload(): bool
    {
        return true; // Semua role bisa download
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('role', 'user');
    }

    // ⭐ TAMBAH INI
    public function scopeTamu($query)
    {
        return $query->where('role', 'tamu');
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
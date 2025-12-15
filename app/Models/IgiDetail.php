<?php
// app/Models/IgiDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class IgiDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'igi_details';

    protected $fillable = [
        'bapb_id',
        'jenis',
        'merk',
        'type',
        'serial_number',
        'mac_address',
        'stb_id',
        'scan_time',
        'scan_by',
        'status_proses'
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    // RELATIONSHIPS
    public function bapb()
    {
        return $this->belongsTo(IgiBapb::class, 'bapb_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scan_by');
    }

    public function ujiFungsi()
    {
        return $this->hasMany(UjiFungsi::class, 'igi_detail_id');
    }

    public function repair()
    {
        return $this->hasMany(Repair::class, 'igi_detail_id');
    }

    public function rekondisi()
    {
        return $this->hasMany(Rekondisi::class, 'igi_detail_id');
    }

    public function serviceHandling()
    {
        return $this->hasMany(ServiceHandling::class, 'igi_detail_id');
    }

    public function packing()
    {
        return $this->hasMany(Packing::class, 'igi_detail_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'igi_detail_id')->orderBy('tanggal', 'desc');
    }

    // SCOPES
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_proses', $status);
    }

    public function scopeByBapb($query, $bapbId)
    {
        return $query->where('bapb_id', $bapbId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('serial_number', 'like', "%{$search}%")
              ->orWhere('mac_address', 'like', "%{$search}%")
              ->orWhere('stb_id', 'like', "%{$search}%");
        });
    }

    // ATTRIBUTES
    public function getLatestActivityAttribute()
    {
        return $this->activityLogs()->first();
    }

    // METHODS
    public function updateStatusProses($newStatus)
    {
        $this->update(['status_proses' => $newStatus]);
    }

    public function logActivity($aktivitas, $result = 'N/A', $userId = null, $keterangan = null)
    {
        ActivityLog::create([
            'igi_detail_id' => $this->id,
            'aktivitas' => $aktivitas,
            'tanggal' => now(),
            'result' => $result,
            'user_id' => $userId ?? Auth::id(),
            'keterangan' => $keterangan
        ]);
    }
}
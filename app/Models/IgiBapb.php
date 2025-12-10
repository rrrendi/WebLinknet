<?php
// app/Models/IgiBapb.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IgiBapb extends Model
{
    use HasFactory;

    protected $table = 'igi_bapb';

    protected $fillable = [
        'pemilik',
        'tanggal_datang',
        'no_ido',
        'wilayah',
        'jumlah',
        'total_scan'
    ];

    protected $casts = [
        'tanggal_datang' => 'date',
        'jumlah' => 'integer',
        'total_scan' => 'integer',
    ];

    // RELATIONSHIPS
    public function details()
    {
        return $this->hasMany(IgiDetail::class, 'bapb_id');
    }

    // SCOPES untuk performa query
    public function scopeByPemilik($query, $pemilik)
    {
        return $query->where('pemilik', $pemilik);
    }

    public function scopeByWilayah($query, $wilayah)
    {
        return $query->where('wilayah', $wilayah);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_datang', [$start, $end]);
    }

    // ATTRIBUTES
    public function getStatusScanAttribute()
    {
        if ($this->total_scan == 0) {
            return 'Belum Scan';
        }
        return $this->total_scan . ' / ' . $this->jumlah;
    }

    public function getIsCompleteAttribute()
    {
        return $this->total_scan >= $this->jumlah;
    }

    // METHODS
    public function incrementTotalScan()
    {
        $this->increment('total_scan');
    }

    public function decrementTotalScan()
    {
        if ($this->total_scan > 0) {
            $this->decrement('total_scan');
        }
    }
}
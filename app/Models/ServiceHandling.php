<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceHandling extends Model
{
    use HasFactory;

    protected $table = 'service_handling';

    protected $fillable = [
        'igi_id',
        'sumber',
        'status',
        'keterangan',
        'waktu_service'
    ];

    protected $casts = [
        'waktu_service' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }
}
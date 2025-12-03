<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjiFungsi extends Model
{
    use HasFactory;

    protected $table = 'uji_fungsi';

    protected $fillable = [
        'igi_id',
        'status',
        'keterangan',
        'waktu_uji'
    ];

    protected $casts = [
        'waktu_uji' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }
}
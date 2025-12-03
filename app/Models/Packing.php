<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    use HasFactory;

    protected $table = 'packing';

    protected $fillable = [
        'igi_id',
        'waktu_packing',
        'kondisi_box',
        'catatan'
    ];

    protected $casts = [
        'waktu_packing' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $table = 'repair';

    protected $fillable = [
        'igi_id',
        'status',
        'jenis_kerusakan',
        'tindakan',
        'waktu_repair'
    ];

    protected $casts = [
        'waktu_repair' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }
}
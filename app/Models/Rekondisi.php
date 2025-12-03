<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekondisi extends Model
{
    use HasFactory;

    protected $table = 'rekondisi';

    protected $fillable = [
        'igi_id',
        'tindakan',
        'waktu_rekondisi'
    ];

    protected $casts = [
        'waktu_rekondisi' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }
}
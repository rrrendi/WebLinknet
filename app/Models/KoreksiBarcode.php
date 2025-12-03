<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoreksiBarcode extends Model
{
    use HasFactory;

    protected $table = 'koreksi_barcode';

    protected $fillable = [
        'igi_id',
        'nama_barang_lama',
        'nama_barang_baru',
        'type_lama',
        'type_baru',
        'tanggal_koreksi',
        'user_id'
    ];

    protected $casts = [
        'tanggal_koreksi' => 'datetime',
    ];

    public function igi()
    {
        return $this->belongsTo(Igi::class, 'igi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php
// ==========================================
// app/Models/IgiMaster.php
// MODEL UNTUK TABEL PERMANEN (AUDIT & HISTORI)
// ==========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IgiMaster extends Model
{
    use HasFactory;

    protected $table = 'igi_master';

    protected $fillable = [
        'no_do',
        'tanggal_datang',
        'nama_barang',
        'type',
        'serial_number',
        'total_scan',
        'status_proses'
    ];

    protected $casts = [
        'tanggal_datang' => 'datetime',
    ];

    // Relasi ke IGI Operasional
    public function igiOperasional()
    {
        return $this->hasOne(Igi::class, 'master_id');
    }
}
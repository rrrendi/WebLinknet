<?php
// ==========================================
// app/Models/Igi.php
// ==========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Igi extends Model
{
    use HasFactory;

    protected $table = 'igi';

    protected $fillable = [
        'master_id',
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

    // Relasi ke Master
    public function master()
    {
        return $this->belongsTo(IgiMaster::class, 'master_id');
    }

    // Relasi ke proses-proses
    public function ujiFungsi()
    {
        return $this->hasMany(UjiFungsi::class, 'igi_id');
    }

    public function repair()
    {
        return $this->hasMany(Repair::class, 'igi_id');
    }

    public function rekondisi()
    {
        return $this->hasMany(Rekondisi::class, 'igi_id');
    }

    public function serviceHandling()
    {
        return $this->hasMany(ServiceHandling::class, 'igi_id');
    }

    public function packing()
    {
        return $this->hasMany(Packing::class, 'igi_id');
    }

    public function koreksiBarcode()
    {
        return $this->hasMany(KoreksiBarcode::class, 'igi_id');
    }
}
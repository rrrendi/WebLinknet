<?php
// app/Models/Packing.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    use HasFactory;
    protected $table = 'packing';
    protected $fillable = ['igi_detail_id', 'packing_time', 'user_id', 'kondisi_box', 'catatan'];
    protected $casts = ['packing_time' => 'datetime'];
    
    public function igiDetail() { return $this->belongsTo(IgiDetail::class, 'igi_detail_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
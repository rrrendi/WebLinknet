<?php
// app/Models/Repair.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;
    protected $table = 'repair';
    protected $fillable = ['igi_detail_id', 'jenis_kerusakan', 'result', 'repair_time', 'user_id', 'catatan'];
    protected $casts = ['repair_time' => 'datetime'];
    
    public function igiDetail() { return $this->belongsTo(IgiDetail::class, 'igi_detail_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
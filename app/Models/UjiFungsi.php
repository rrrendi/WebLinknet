<?php
// app/Models/UjiFungsi.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjiFungsi extends Model
{
    use HasFactory;
    protected $table = 'uji_fungsi';
    protected $fillable = ['igi_detail_id', 'result', 'uji_fungsi_time', 'user_id'];
    protected $casts = ['uji_fungsi_time' => 'datetime'];
    
    public function igiDetail() { return $this->belongsTo(IgiDetail::class, 'igi_detail_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
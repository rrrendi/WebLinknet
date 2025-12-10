<?php
// app/Models/Rekondisi.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekondisi extends Model
{
    use HasFactory;
    protected $table = 'rekondisi';
    protected $fillable = ['igi_detail_id', 'rekondisi_time', 'user_id'];
    protected $casts = ['rekondisi_time' => 'datetime'];
    
    public function igiDetail() { return $this->belongsTo(IgiDetail::class, 'igi_detail_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
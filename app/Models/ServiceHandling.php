<?php
// app/Models/ServiceHandling.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceHandling extends Model
{
    use HasFactory;
    protected $table = 'service_handling';
    protected $fillable = ['igi_detail_id', 'service_time', 'user_id', 'catatan'];
    protected $casts = ['service_time' => 'datetime'];
    
    public function igiDetail() { return $this->belongsTo(IgiDetail::class, 'igi_detail_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
<?php
// app/Models/ActivityLog.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $fillable = [
        'igi_detail_id',
        'aktivitas',
        'tanggal',
        'result',
        'user_id',
        'keterangan',
        'data_lama',
        'data_baru'
    ];
    
    protected $casts = [
        'tanggal' => 'datetime',
        'data_lama' => 'array',
        'data_baru' => 'array'
    ];
    
    public function igiDetail()
    {
        return $this->belongsTo(IgiDetail::class, 'igi_detail_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeByBarang($query, $igiDetailId)
    {
        return $query->where('igi_detail_id', $igiDetailId);
    }
    
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
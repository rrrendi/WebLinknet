<?php
// app/Models/MasterType.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterType extends Model
{
    use HasFactory;
    protected $table = 'master_type';
    protected $fillable = ['merk_id', 'type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    
    public function merk()
    {
        return $this->belongsTo(MasterMerk::class, 'merk_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
<?php
// app/Models/MasterMerk.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMerk extends Model
{
    use HasFactory;
    protected $table = 'master_merk';
    protected $fillable = ['jenis', 'merk', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    
    public function types()
    {
        return $this->hasMany(MasterType::class, 'merk_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
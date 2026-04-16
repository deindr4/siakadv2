<?php
// ============================================================
// app/Models/KategoriPrestasi.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPrestasi extends Model
{
    protected $table = 'kategori_prestasi';
    protected $fillable = ['nama', 'jenis', 'warna', 'is_aktif'];
    protected $casts = ['is_aktif' => 'boolean'];

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'kategori_id');
    }

    public function jenisLabel(): string
    {
        return $this->jenis === 'akademik' ? 'Akademik' : 'Non-Akademik';
    }
}

<?php
// ============================================================
// app/Models/PrestasiSiswa.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    protected $table = 'prestasi_siswa';
    protected $fillable = ['prestasi_id', 'siswa_id', 'peran'];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumnis';

    protected $fillable = [
        'semester_id', 'siswa_id', 'peserta_didik_id',
        'nisn', 'nipd', 'nama', 'jenis_kelamin', 'nik',
        'tempat_lahir', 'tanggal_lahir', 'agama',
        'nama_rombel', 'tingkat_pendidikan_id', 'kurikulum', 'sekolah_asal',
        'tahun_lulus', 'tanggal_lulus', 'no_ijazah', 'no_skhun', 'nilai_rata', 'keterangan',
        'nama_ayah', 'nama_ibu', 'nama_wali', 'no_hp_ortu',
        'no_hp', 'email', 'sumber_data',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_lulus' => 'date',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

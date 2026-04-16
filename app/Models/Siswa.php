<?php
//app/Models/Siswa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'semester_id', 'user_id', 'nisn', 'nipd', 'peserta_didik_id',
        'registrasi_id', 'nama', 'jenis_kelamin', 'nik', 'tempat_lahir',
        'tanggal_lahir', 'agama', 'no_hp', 'no_hp_rumah', 'email',
        'tinggi_badan', 'berat_badan', 'kebutuhan_khusus',
        'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu',
        'nama_wali', 'pekerjaan_wali', 'no_hp_ortu', 'anak_keberapa',
        'nama_rombel', 'rombongan_belajar_id', 'tingkat_pendidikan_id',
        'kurikulum', 'sekolah_asal', 'tanggal_masuk_sekolah',
        'jenis_pendaftaran', 'status', 'is_archived', 'semester_arsip',
        'sumber_data', 'last_sync_dapodik',
        "status_mutasi",
        "tanggal_mutasi",
        "keterangan_mutasi",
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'tanggal_masuk_sekolah' => 'date',
        'tanggal_mutasi'        => 'date',
        'last_sync_dapodik'   => 'datetime',
        'is_archived'         => 'boolean',
    ];

    public function semester()   { return $this->belongsTo(Semester::class); }
    public function user()       { return $this->belongsTo(User::class); }

    public function getJkLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getTingkatLabelAttribute()
    {
        $map = ['10' => 'X', '11' => 'XI', '12' => 'XII'];
        return $map[$this->tingkat_pendidikan_id] ?? $this->tingkat_pendidikan_id;
    }

    public function pelanggaranAktif()
    {
        return $this->hasMany(PelanggaranSiswa::class)
            ->where('status', '!=', 'dibatalkan');
    }

    public function pelanggaran()
    {
        return $this->hasMany(PelanggaranSiswa::class);
    }
}

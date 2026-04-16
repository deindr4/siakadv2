<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PelanggaranSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'semester_id', 'siswa_id', 'jenis_pelanggaran_id',
        'dicatat_oleh', 'tanggal', 'poin', 'keterangan',
        'tindakan', 'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class);
    }

    public function dicatatOleh()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

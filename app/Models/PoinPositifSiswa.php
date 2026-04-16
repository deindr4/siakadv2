<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinPositifSiswa extends Model
{
    protected $table = 'poin_positif_siswa';

    protected $fillable = [
        'semester_id', 'siswa_id', 'jenis_kegiatan_id',
        'dicatat_oleh', 'tanggal', 'poin', 'keterangan', 'bukti', 'status',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function siswa()        { return $this->belongsTo(Siswa::class); }
    public function jenisKegiatan(){ return $this->belongsTo(JenisKegiatanPositif::class, 'jenis_kegiatan_id'); }
    public function dicatatOleh()  { return $this->belongsTo(User::class, 'dicatat_oleh'); }
    public function semester()     { return $this->belongsTo(Semester::class); }
}

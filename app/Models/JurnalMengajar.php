<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalMengajar extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'semester_id', 'guru_id', 'mata_pelajaran_id',
        'nama_rombel', 'rombongan_belajar_id',
        'tanggal', 'jam_ke', 'jam_mulai', 'jam_selesai', 'pertemuan_ke',
        'materi', 'kegiatan', 'catatan',
        'jumlah_hadir', 'jumlah_tidak_hadir',
        'foto_pendukung', 'tanda_tangan', 'status',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_mulai'  => 'datetime:H:i',
        'jam_selesai'=> 'datetime:H:i',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}

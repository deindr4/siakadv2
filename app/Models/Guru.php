<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'ptk_id', 'ptk_terdaftar_id', 'nuptk', 'nip', 'nik',
        'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
        'jenis_ptk', 'jabatan', 'status_kepegawaian', 'pangkat_golongan',
        'pendidikan_terakhir', 'bidang_studi', 'tahun_ajaran',
        'tanggal_surat_tugas', 'no_hp', 'email',
        'status', 'is_archived', 'tahun_arsip',
        'sumber_data', 'last_sync_dapodik',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'tanggal_surat_tugas' => 'date',
        'last_sync_dapodik'   => 'datetime',
        'is_archived'         => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function getJkLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}

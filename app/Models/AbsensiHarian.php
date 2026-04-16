<?php

// app/Models/AbsensiHarian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    protected $table = 'absensi_harian';

    protected $fillable = [
        'semester_id', 'rombongan_belajar_id', 'nama_rombel',
        'tanggal', 'guru_id', 'nama_guru', 'diabsen_pada',
        'ip_address', 'is_locked', 'locked_by', 'locked_at', 'catatan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'diabsen_pada' => 'datetime',
        'locked_at'    => 'datetime',
        'is_locked'    => 'boolean',
    ];

    public function semester()        { return $this->belongsTo(Semester::class); }
    public function rombel()          { return $this->belongsTo(Rombel::class, 'rombongan_belajar_id'); }
    public function guru()            { return $this->belongsTo(Guru::class); }
    public function lockedBy()        { return $this->belongsTo(User::class, 'locked_by'); }
    public function absensiSiswa()    { return $this->hasMany(AbsensiSiswa::class); }

    // Stat cepat
    public function jumlahHadir()     { return $this->absensiSiswa()->where('status', 'H')->count(); }
    public function jumlahSakit()     { return $this->absensiSiswa()->where('status', 'S')->count(); }
    public function jumlahIzin()      { return $this->absensiSiswa()->where('status', 'I')->count(); }
    public function jumlahAlpa()      { return $this->absensiSiswa()->where('status', 'A')->count(); }
    public function jumlahDispensasi(){ return $this->absensiSiswa()->where('status', 'D')->count(); }
}

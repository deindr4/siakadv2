<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    protected $table = 'absensi_siswa';

    protected $fillable = [
        'absensi_harian_id', 'siswa_id', 'status', 'keterangan',
    ];

    public function absensiHarian() { return $this->belongsTo(AbsensiHarian::class); }
    public function siswa()         { return $this->belongsTo(Siswa::class); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'H' => 'Hadir',
            'S' => 'Sakit',
            'I' => 'Izin',
            'A' => 'Alpa',
            'D' => 'Dispensasi',
            default => '-',
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'H' => '#16a34a',
            'S' => '#0284c7',
            'I' => '#d97706',
            'A' => '#dc2626',
            'D' => '#7c3aed',
            default => '#94a3b8',
        };
    }

    public function statusBg(): string
    {
        return match($this->status) {
            'H' => '#dcfce7',
            'S' => '#e0f2fe',
            'I' => '#fef3c7',
            'A' => '#fee2e2',
            'D' => '#ede9fe',
            default => '#f1f5f9',
        };
    }
}

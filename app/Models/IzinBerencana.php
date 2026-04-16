<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IzinBerencana extends Model
{
    protected $table = 'izin_berencana';

    protected $fillable = [
        'nomor_izin', 'semester_id', 'siswa_id',
        'jenis', 'alasan', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari',
        'nama_ortu', 'no_hp_ortu', 'ttd_ortu',
        'status', 'disetujui_oleh', 'disetujui_pada',
        'jumlah_hari_disetujui', 'catatan_approver', 'lampiran',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'disetujui_pada'  => 'datetime',
    ];

    // Relations
    public function siswa()         { return $this->belongsTo(Siswa::class); }
    public function semester()      { return $this->belongsTo(Semester::class); }
    public function disetujuiOleh() { return $this->belongsTo(User::class, 'disetujui_oleh'); }

    // Jenis label
    public static function jenisList(): array
    {
        return [
            'keperluan_keluarga'  => '👨‍👩‍👧 Keperluan Keluarga',
            'perjalanan_wisata'   => '✈️ Perjalanan / Wisata Keluarga',
            'lainnya'             => '📝 Lainnya',
        ];
    }

    // Status label & color
    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'    => '#d97706',
            'disetujui'  => '#16a34a',
            'ditolak'    => '#dc2626',
            'dibatalkan' => '#94a3b8',
            default      => '#94a3b8',
        };
    }

    public function statusBg(): string
    {
        return match($this->status) {
            'pending'    => '#fef3c7',
            'disetujui'  => '#dcfce7',
            'ditolak'    => '#fee2e2',
            'dibatalkan' => '#f1f5f9',
            default      => '#f1f5f9',
        };
    }

    // Jumlah hari efektif yang disetujui
    public function hariEfektif(): int
    {
        return $this->jumlah_hari_disetujui ?? $this->jumlah_hari;
    }

    // Generate nomor izin
    public static function generateNomor(): string
    {
        $year  = date('Y');
        $last  = static::whereYear('created_at', $year)->max('nomor_izin');
        $seq   = $last ? (intval(substr($last, -4)) + 1) : 1;
        return 'IZN-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // Cek apakah siswa melebihi batas 2 hari mandiri
    public function melebihiBatasMandiri(): bool
    {
        return $this->jumlah_hari > 2;
    }
}

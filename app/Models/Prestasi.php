<?php
// ============================================================
// app/Models/Prestasi.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestasi extends Model
{
    use SoftDeletes;

    protected $table = 'prestasi';
    protected $fillable = [
        'kategori_id', 'semester_id', 'nama_lomba', 'penyelenggara',
        'tingkat', 'tanggal', 'tempat', 'juara', 'juara_urut',
        'tipe', 'nama_tim', 'status', 'diverifikasi_oleh', 'diverifikasi_pada',
        'catatan_verifikasi', 'dibuat_oleh', 'role_pembuat',
        'file_sertifikat', 'file_sertifikat_original', 'keterangan',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'diverifikasi_pada' => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────
    public function kategori()
    {
        return $this->belongsTo(KategoriPrestasi::class, 'kategori_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'prestasi_siswa', 'prestasi_id', 'siswa_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

    public function prestasiSiswa()
    {
        return $this->hasMany(PrestasiSiswa::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function tingkatLabel(): string
    {
        return match($this->tingkat) {
            'sekolah'       => 'Sekolah',
            'kecamatan'     => 'Kecamatan',
            'kabupaten'     => 'Kabupaten/Kota',
            'provinsi'      => 'Provinsi',
            'nasional'      => 'Nasional',
            'internasional' => 'Internasional',
            default         => ucfirst($this->tingkat),
        };
    }

    public function tingkatColor(): string
    {
        return match($this->tingkat) {
            'sekolah'       => '#64748b',
            'kecamatan'     => '#0891b2',
            'kabupaten'     => '#16a34a',
            'provinsi'      => '#d97706',
            'nasional'      => '#dc2626',
            'internasional' => '#7c3aed',
            default         => '#374151',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'      => 'Menunggu Verifikasi',
            'diverifikasi' => 'Terverifikasi',
            'ditolak'      => 'Ditolak',
            default        => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'      => '#d97706',
            'diverifikasi' => '#16a34a',
            'ditolak'      => '#dc2626',
            default        => '#64748b',
        };
    }

    public function statusBg(): string
    {
        return match($this->status) {
            'pending'      => '#fef3c7',
            'diverifikasi' => '#dcfce7',
            'ditolak'      => '#fee2e2',
            default        => '#f1f5f9',
        };
    }

    public function hasSertifikat(): bool
    {
        return !empty($this->file_sertifikat);
    }

    public function sertifikatUrl(): ?string
    {
        return $this->file_sertifikat ? asset('storage/' . $this->file_sertifikat) : null;
    }

    // Scope
    public function scopeVerified($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

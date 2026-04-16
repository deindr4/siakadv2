<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $fillable = [
        'semester_id',
        'rombongan_belajar_id',
        'nama_rombel',
        'tingkat',
        'jurusan',
        'kurikulum',
        'jumlah_siswa',
        'is_archived',
        'jenis_rombel',
        'jenis_rombel_str',
        'wali_kelas',
        'ptk_id',
    ];

    protected $casts = ['is_archived' => 'boolean'];

    public function semester() { return $this->belongsTo(Semester::class); }
    public function siswas()   { return $this->hasMany(Siswa::class, 'rombongan_belajar_id', 'rombongan_belajar_id'); }

    // Scopes
    public function scopeKelas($query)
    {
        return $query->where('jenis_rombel', '1');
    }

    public function scopeMapel($query)
    {
        return $query->where('jenis_rombel', '16');
    }

    public function scopeEkskul($query)
    {
        return $query->where('jenis_rombel', '51');
    }

    // Helpers
    public function isKelas(): bool  { return $this->jenis_rombel == '1'; }
    public function isMapel(): bool  { return $this->jenis_rombel == '16'; }
    public function isEkskul(): bool { return $this->jenis_rombel == '51'; }

    public function jenisLabel(): string
    {
        return match((string)$this->jenis_rombel) {
            '1'     => '🏫 Kelas',
            '16'    => '📚 Mapel Pilihan',
            '51'    => '⚽ Ekskul',
            default => $this->jenis_rombel_str ?? '-',
        };
    }

    public function jenisBadgeStyle(): string
    {
        return match((string)$this->jenis_rombel) {
            '1'     => 'background:#fef3c7;color:#d97706;',
            '16'    => 'background:#dbeafe;color:#1d4ed8;',
            '51'    => 'background:#ede9fe;color:#7c3aed;',
            default => 'background:#f1f5f9;color:#64748b;',
        };
    }

}


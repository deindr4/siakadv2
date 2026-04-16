<?php

// app/Models/Semester.php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'semester_id', 'nama', 'tahun_ajaran',
        'tipe', 'is_aktif', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected $casts = [
        'is_aktif'        => 'boolean',
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public static function aktif(): ?self
    {
        return self::where('is_aktif', true)->first();
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function rombels()
    {
        return $this->hasMany(Rombel::class);
    }

    // Parse semester_id Dapodik → nama
    // "20252" → "Semester Genap 2025/2026"
    public static function parseNama(string $semesterId): string
    {
        $tahun = substr($semesterId, 0, 4);
        $tipe  = substr($semesterId, 4, 1);
        $tahunAjaran = $tahun . '/' . ((int)$tahun + 1);
        return $tipe === '1'
            ? "Semester Ganjil {$tahunAjaran}"
            : "Semester Genap {$tahunAjaran}";
    }

    public static function parseTipe(string $semesterId): string
    {
        return substr($semesterId, 4, 1) === '1' ? 'ganjil' : 'genap';
    }

    public static function parseTahunAjaran(string $semesterId): string
    {
        $tahun = substr($semesterId, 0, 4);
        return $tahun . '/' . ((int)$tahun + 1);
    }
}

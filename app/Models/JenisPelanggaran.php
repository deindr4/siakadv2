<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';

    protected $fillable = [
        'kode', 'nama', 'deskripsi', 'kategori', 'poin', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pelanggaranSiswa()
    {
        return $this->hasMany(PelanggaranSiswa::class);
    }

    public function kategoriLabel(): string
    {
        return match($this->kategori) {
            'ringan' => '🟡 Ringan',
            'sedang' => '🟠 Sedang',
            'berat'  => '🔴 Berat',
            default  => $this->kategori,
        };
    }

    public function kategoriBadgeStyle(): string
    {
        return match($this->kategori) {
            'ringan' => 'background:#fef3c7;color:#d97706;',
            'sedang' => 'background:#fed7aa;color:#ea580c;',
            'berat'  => 'background:#fee2e2;color:#dc2626;',
            default  => 'background:#f1f5f9;color:#64748b;',
        };
    }
}

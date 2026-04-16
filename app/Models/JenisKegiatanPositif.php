<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKegiatanPositif extends Model
{
    protected $table = 'jenis_kegiatan_positif';

    protected $fillable = [
        'nama', 'kategori', 'poin', 'keterangan', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function poinSiswa()
    {
        return $this->hasMany(PoinPositifSiswa::class, 'jenis_kegiatan_id');
    }

    public static function kategoriList(): array
    {
        return [
            'akademik'          => '🎓 Akademik (Olimpiade, Lomba)',
            'olahraga_seni'     => '🏆 Olahraga & Seni',
            'organisasi'        => '🤝 Organisasi (OSIS, Pramuka, dll)',
            'sosial_keagamaan'  => '🕌 Sosial & Keagamaan',
            'lainnya'           => '⭐ Lainnya',
        ];
    }
}

<?php
// database/seeders/KategoriPrestasiSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriPrestasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Akademik / Olimpiade', 'jenis' => 'akademik',     'warna' => '#6366f1'],
            ['nama' => 'Karya Tulis / Penelitian', 'jenis' => 'akademik', 'warna' => '#0284c7'],
            ['nama' => 'Olahraga',              'jenis' => 'non_akademik', 'warna' => '#16a34a'],
            ['nama' => 'Seni & Budaya',         'jenis' => 'non_akademik', 'warna' => '#d97706'],
            ['nama' => 'Keagamaan',             'jenis' => 'non_akademik', 'warna' => '#7c3aed'],
            ['nama' => 'Pramuka / OSIS',        'jenis' => 'non_akademik', 'warna' => '#dc2626'],
            ['nama' => 'Teknologi / IT',        'jenis' => 'akademik',     'warna' => '#0891b2'],
        ];

        foreach ($data as $item) {
            DB::table('kategori_prestasi')->insertOrIgnore(array_merge($item, [
                'is_aktif'   => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

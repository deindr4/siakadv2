<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role kalau belum ada (tidak error kalau sudah ada)
        $roles = [
            'admin',
            'kepala_sekolah',
            'wakil_kepala_sekolah',
            'guru',
            'bk',
            'tata_usaha',
            'siswa',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Buat user admin kalau belum ada
        $admin = User::firstOrCreate(
            ['email' => 'admin@siakad.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        $admin->assignRole('admin');
    }
}

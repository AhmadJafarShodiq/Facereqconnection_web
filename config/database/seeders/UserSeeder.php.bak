<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // GURU
        $guru = User::create([
            'username' => 'guru01',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'is_active' => true,
        ]);

        Profile::create([
            'user_id' => $guru->id,
            'nama_lengkap' => 'Budi Santoso',
            'nip_nis' => '1987654321',
            'jabatan_kelas' => 'Guru Matematika',
            'instansi' => 'SMP Negeri 1',
        ]);

        // SISWA
        $siswa = User::create([
            'username' => 'siswa01',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        Profile::create([
            'user_id' => $siswa->id,
            'nama_lengkap' => 'Andi Pratama',
            'nip_nis' => '123456789',
            'jabatan_kelas' => 'Kelas 9A',
            'instansi' => 'SMP Negeri 1',
        ]);
    }
}

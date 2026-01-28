<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Kelas;
use App\Models\Subject;
use App\Models\School;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info('🌱 DatabaseSeeder started...');
        // =====================
        // 1. BUAT SEKOLAH
        // =====================
        Log::info('Creating school...');
        $school = School::create([
            'nama_sekolah' => 'SMA Merdeka Jaya',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 1000, // 1km radius
        ]);

        // =====================
        // 2. BUAT KELAS
        // =====================
        $kelas1a = Kelas::create(['nama_kelas' => '1A']);
        $kelas1b = Kelas::create(['nama_kelas' => '1B']);
        $kelas2a = Kelas::create(['nama_kelas' => '2A']);

        // =====================
        // 3. BUAT GURU
        // =====================
        $guru1 = User::create([
            'username' => 'budi_guru',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        Profile::create([
            'user_id' => $guru1->id,
            'nama_lengkap' => 'Budi Santoso',
            'nip_nis' => '19850510201001',
            'jabatan_kelas' => 'Guru Matematika',
            'instansi' => 'SMA Merdeka Jaya',
            'kelas_id' => $kelas1a->id,
        ]);

        $guru2 = User::create([
            'username' => 'ani_guru',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        Profile::create([
            'user_id' => $guru2->id,
            'nama_lengkap' => 'Ani Wijaya',
            'nip_nis' => '19880620201002',
            'jabatan_kelas' => 'Guru IPA',
            'instansi' => 'SMA Merdeka Jaya',
            'kelas_id' => $kelas1a->id,
        ]);

        $guru3 = User::create([
            'username' => 'citra_guru',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'is_active' => true,
        ]);
        Profile::create([
            'user_id' => $guru3->id,
            'nama_lengkap' => 'Citra Dewi',
            'nip_nis' => '19900315201003',
            'jabatan_kelas' => 'Guru Bahasa Indonesia',
            'instansi' => 'SMA Merdeka Jaya',
            'kelas_id' => $kelas1a->id,
        ]);

        // =====================
        // 4. BUAT SISWA
        // =====================
        $siswa_data = [
            ['ahmad_rifai', 'Ahmad Rifai', '001'],
            ['budi_widodo', 'Budi Widodo', '002'],
            ['citra_kusuma', 'Citra Kusuma', '003'],
            ['dani_hermawan', 'Dani Hermawan', '004'],
            ['eka_putri', 'Eka Putri', '005'],
        ];

        $siswa_users = [];
        foreach ($siswa_data as $data) {
            $user = User::create([
                'username' => $data[0],
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'is_active' => true,
            ]);
            Profile::create([
                'user_id' => $user->id,
                'nama_lengkap' => $data[1],
                'nip_nis' => $data[2],
                'kelas_id' => $kelas1a->id,
            ]);
            $siswa_users[] = $user;
        }

        // =====================
        // 5. BUAT MAPEL
        // =====================
        $mapel_data = [
            ['Matematika', $kelas1a->id],
            ['IPA', $kelas1a->id],
            ['Bahasa Indonesia', $kelas1a->id],
            ['Bahasa Inggris', $kelas1a->id],
        ];

        $subjects = [];
        foreach ($mapel_data as $m) {
            $subjects[] = Subject::create([
                'nama_mapel' => $m[0],
                'kelas_id' => $m[1],
            ]);
        }

        // =====================
        // 6. ASSIGN GURU KE MAPEL
        // =====================
        $guru1->subjects()->attach($subjects[0]->id); // Budi = Matematika
        $guru2->subjects()->attach($subjects[1]->id); // Ani = IPA
        $guru3->subjects()->attach($subjects[2]->id); // Citra = Bahasa Indonesia

        // =====================
        // 7. BUAT JADWAL PELAJARAN
        // =====================
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jam_pelajaran = [
            ['08:00', '09:30'],
            ['09:45', '11:15'],
            ['12:00', '13:30'],
            ['13:45', '15:15'],
        ];

        // Jadwal Guru 1 (Matematika)
        foreach ($hari_list as $hari) {
            Schedule::create([
                'user_id' => $guru1->id,
                'subject_id' => $subjects[0]->id,
                'kelas_id' => $kelas1a->id,
                'hari' => $hari,
                'jam_mulai' => $jam_pelajaran[0][0],
                'jam_selesai' => $jam_pelajaran[0][1],
                'ruangan' => '1A',
            ]);
        }

        // Jadwal Guru 2 (IPA)
        foreach ($hari_list as $hari) {
            Schedule::create([
                'user_id' => $guru2->id,
                'subject_id' => $subjects[1]->id,
                'kelas_id' => $kelas1a->id,
                'hari' => $hari,
                'jam_mulai' => $jam_pelajaran[1][0],
                'jam_selesai' => $jam_pelajaran[1][1],
                'ruangan' => '1A',
            ]);
        }

        // Jadwal Guru 3 (Bahasa Indonesia)
        foreach ($hari_list as $hari) {
            Schedule::create([
                'user_id' => $guru3->id,
                'subject_id' => $subjects[2]->id,
                'kelas_id' => $kelas1a->id,
                'hari' => $hari,
                'jam_mulai' => $jam_pelajaran[2][0],
                'jam_selesai' => $jam_pelajaran[2][1],
                'ruangan' => '1A',
            ]);
        }

        echo "✅ Seeder selesai!\n";
        echo "\n📝 Data yang dibuat:\n";
        echo "- 1 Sekolah\n";
        echo "- 3 Kelas\n";
        echo "- 3 Guru\n";
        echo "- 5 Siswa\n";
        echo "- 4 Mapel\n";
        echo "- 15 Jadwal Pelajaran\n";
        echo "\n🔑 Akun untuk testing:\n";
        echo "GURU:\n";
        echo "  Username: budi_guru | Password: password123\n";
        echo "  Username: ani_guru | Password: password123\n";
        echo "  Username: citra_guru | Password: password123\n";
        echo "\nSISWA:\n";
        echo "  Username: ahmad_rifai | Password: password123\n";
        echo "  Username: budi_widodo | Password: password123\n";
        echo "  Username: citra_kusuma | Password: password123\n";
    }
}

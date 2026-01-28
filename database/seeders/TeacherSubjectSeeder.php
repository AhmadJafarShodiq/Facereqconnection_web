<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AMBIL SEMUA GURU
        $teachers = User::where('role', 'guru')->get();
        
        // AMBIL SEMUA MAPEL
        $subjects = Subject::all();

        // ASSIGN SETIAP GURU KE MAPEL (SESUAIKAN LOGIC NYA)
        foreach ($teachers as $teacher) {
            // Contoh: guru mengajar 2-3 mapel acak di kelasnya
            $randomSubjects = $subjects
                ->where('kelas_id', $teacher->profile?->kelas_id) // MAPEL SESUAI KELAS GURU
                ->random(min(2, $subjects->count())); // MAX 2-3 MAPEL

            foreach ($randomSubjects as $subject) {
                // CEK TIDAK DUPLICATE
                if (!$teacher->subjects->contains($subject->id)) {
                    $teacher->subjects()->attach($subject->id);
                }
            }
        }

        echo "✅ Seeder guru-mapel selesai!\n";
    }
}

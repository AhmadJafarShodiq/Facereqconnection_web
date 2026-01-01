<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\School;
class SchoolSeeder extends Seeder
{
    public function run()
    {
        School::create([
            'nama_sekolah' => 'SMP Negeri 1',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius' => 100
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\JamApel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JamApelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JamApel::updateOrCreate(
            ['type' => 'pagi'],
            ['start_time' => '07:00:00', 'end_time' => '09:00:00']
        );

        JamApel::updateOrCreate(
            ['type' => 'sore'],
            ['start_time' => '15:00:00', 'end_time' => '17:00:00']
        );
    }
}

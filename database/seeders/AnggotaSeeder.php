<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subdis;
use Illuminate\Database\Seeder;
use Database\Seeders\SubdisSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create subdis if they don't exist
        if (Subdis::count() === 0) {
            $this->call(SubdisSeeder::class);
        }

        $subdis = Subdis::all();

        // Create 10 anggota for each subdis
        foreach ($subdis as $subdisItem) {
            User::factory()->count(10)->create([
                'role' => 'personil',
                'subdis_id' => $subdisItem->id,
            ]);
        }
    }
}

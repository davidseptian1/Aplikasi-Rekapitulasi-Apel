<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Biodata;
use App\Models\Jabatan;
use App\Models\Pangkat;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\JabatanSeeder;
use Database\Seeders\PangkatSeeder;

class BiodataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure required data exists
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (Pangkat::count() === 0) {
            $this->call(PangkatSeeder::class);
        }

        if (Jabatan::count() === 0) {
            $this->call(JabatanSeeder::class);
        }

        $users = User::all();
        $pangkats = Pangkat::all();
        $jabatans = Jabatan::all();

        foreach ($users as $user) {
            Biodata::create([
                'user_id' => $user->id,
                'pangkat_id' => $pangkats->random()->id,
                'jabatan_id' => $jabatans->random()->id,
            ]);
        }
    }
}

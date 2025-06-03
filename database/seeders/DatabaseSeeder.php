<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SubdisSeeder;
use Database\Seeders\BiodataSeeder;
use Database\Seeders\JabatanSeeder;
use Database\Seeders\PangkatSeeder;
use Database\Seeders\KeteranganSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PangkatSeeder::class,
            JabatanSeeder::class,
            KeteranganSeeder::class,
            BiodataSeeder::class,
            SubdisSeeder::class,
            AnggotaSeeder::class,
        ]);
    }
}

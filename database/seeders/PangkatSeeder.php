<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use Illuminate\Database\Seeder;

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pangkats = [
            ['name' => 'Kolonel', 'nilai_pangkat' => 100],
            ['name' => 'Letkol', 'nilai_pangkat' => 90],
            ['name' => 'Mayor', 'nilai_pangkat' => 80],
            ['name' => 'Serma', 'nilai_pangkat' => 70],
            // Tambahkan pangkat lain dengan nilainya
        ];

        foreach ($pangkats as $pangkat) {
            Pangkat::create($pangkat);
        }
    }
}

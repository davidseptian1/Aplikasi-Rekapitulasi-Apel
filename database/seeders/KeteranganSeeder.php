<?php

namespace Database\Seeders;

use App\Models\Keterangan;
use Illuminate\Database\Seeder;

class KeteranganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keterangans = [
            ['name' => 'Hadir'],
            ['name' => 'Izin'],
            ['name' => 'Sakit'],
            ['name' => 'Cuti'],
            ['name' => 'Dinas Luar'],
            ['name' => 'Tugas Belajar'],
            ['name' => 'Libur Nasional'],
            ['name' => 'Alpha'],
            ['name' => 'Terlambat'],
            ['name' => 'Pulang Cepat'],
        ];

        foreach ($keterangans as $keterangan) {
            Keterangan::create($keterangan);
        }
    }
}

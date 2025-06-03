<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin
        User::create([
            'name' => 'Super Admin',
            'no_telpon' => '081234567890',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        // Create pokmin
        $pokminUsers = [
            ['name' => 'Pokmin 01', 'no_telpon' => '083456789011', 'username' => 'pokmin01', 'email' => 'pokmin01@example.com'],
            ['name' => 'Pokmin 02', 'no_telpon' => '083456789012', 'username' => 'pokmin02', 'email' => 'pokmin02@example.com'],
            ['name' => 'Pokmin 03', 'no_telpon' => '083456789013', 'username' => 'pokmin03', 'email' => 'pokmin03@example.com'],
            ['name' => 'Pokmin 04', 'no_telpon' => '083456789014', 'username' => 'pokmin04', 'email' => 'pokmin04@example.com'],
            ['name' => 'Pokmin 05', 'no_telpon' => '083456789015', 'username' => 'pokmin05', 'email' => 'pokmin05@example.com'],
        ];

        // Create pokmin
        foreach ($pokminUsers as $pokminUser) {
            User::create([
                'name' => $pokminUser['name'],
                'no_telpon' => $pokminUser['no_telpon'],
                'username' => $pokminUser['username'],
                'email' => $pokminUser['email'],
                'password' => Hash::make('password'),
                'role' => 'pokmin',
                'email_verified_at' => now(),
            ]);
        }

        // Create piket
        $piketUsers = [
            ['name' => 'Piket 01', 'no_telpon' => '083456789011', 'username' => 'piket01', 'email' => 'piket01@example.com'],
            ['name' => 'Piket 02', 'no_telpon' => '083456789012', 'username' => 'piket02', 'email' => 'piket02@example.com'],
            ['name' => 'Piket 03', 'no_telpon' => '083456789013', 'username' => 'piket03', 'email' => 'piket03@example.com'],
            ['name' => 'Piket 04', 'no_telpon' => '083456789014', 'username' => 'piket04', 'email' => 'piket04@example.com'],
            ['name' => 'Piket 05', 'no_telpon' => '083456789015', 'username' => 'piket05', 'email' => 'piket05@example.com'],
        ];

        foreach ($piketUsers as $piketUser) {
            User::create([
                'name' => $piketUser['name'],
                'no_telpon' => $piketUser['no_telpon'],
                'username' => $piketUser['username'],
                'email' => $piketUser['email'],
                'password' => Hash::make('password'),
                'role' => 'piket',
                'email_verified_at' => now(),
            ]);
        }

        // Create pimpinan
        $pimpinanUsers = [
            ['name' => 'Pimpinan 01', 'no_telpon' => '083456789011', 'username' => 'pimpinan01', 'email' => 'pimpinan01@example.com'],
            ['name' => 'Pimpinan 02', 'no_telpon' => '083456789012', 'username' => 'pimpinan02', 'email' => 'pimpinan02@example.com'],
            ['name' => 'Pimpinan 03', 'no_telpon' => '083456789013', 'username' => 'pimpinan03', 'email' => 'pimpinan03@example.com'],
            ['name' => 'Pimpinan 04', 'no_telpon' => '083456789014', 'username' => 'pimpinan04', 'email' => 'pimpinan04@example.com'],
            ['name' => 'Pimpinan 05', 'no_telpon' => '083456789015', 'username' => 'pimpinan05', 'email' => 'pimpinan05@example.com'],
        ];

        foreach ($pimpinanUsers as $pimpinanUser) {
            User::create([
                'name' => $pimpinanUser['name'],
                'no_telpon' => $pimpinanUser['no_telpon'],
                'username' => $pimpinanUser['username'],
                'email' => $pimpinanUser['email'],
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
            ]);
        }
    }
}

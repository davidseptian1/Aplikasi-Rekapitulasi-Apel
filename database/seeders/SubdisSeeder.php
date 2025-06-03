<?php

namespace Database\Seeders;

use App\Models\Subdis;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubdisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::whereIn('role', ['pokmin'])
            ->limit(5)
            ->get();

        $subdis = [
            ['name' => 'Subdirektorat A', 'user_id' => $users->count() > 0 ? $users[0]->id : null],
            ['name' => 'Subdirektorat B', 'user_id' => $users->count() > 1 ? $users[1]->id : null],
            ['name' => 'Subdirektorat C', 'user_id' => $users->count() > 2 ? $users[2]->id : null],
            ['name' => 'Subdirektorat D', 'user_id' => $users->count() > 3 ? $users[3]->id : null],
            ['name' => 'Subdirektorat E', 'user_id' => $users->count() > 4 ? $users[4]->id : null],
        ];

        foreach ($subdis as $subdi) {
            Subdis::create($subdi);
        }
    }
}

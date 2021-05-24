<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListHadist extends Seeder
{
    public function run()
    {
        DB::table('playlists')->insert([
            ['user_id' => 1, 'name' => 'Murrotal'],
            ['user_id' => 1, 'name' => 'Hadist'],
            ['user_id' => 1, 'name' => 'Hadist Tahajud'],
            ['user_id' => 1, 'name' => 'Hadist Subuh'],
            ['user_id' => 1, 'name' => 'Hadist Duha'],
            ['user_id' => 1, 'name' => 'Hadist Dzuhur'],
            ['user_id' => 1, 'name' => 'Hadist Ashar'],
            ['user_id' => 1, 'name' => 'Hadist Maghrib'],
            ['user_id' => 1, 'name' => 'Hadist Isya']
        ]);
    }
}

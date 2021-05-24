<?php

namespace Database\Seeders;

use App\Models\SongVolume;
use Illuminate\Database\Seeder;

class SongVolumesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->toDateTimeString();

        SongVolume::insert([
            ['name_volume' => 'murotalsesisatu', 'presentage' => '89', 'created_at' => $now, 'updated_at' => $now],
            ['name_volume' => 'murotalsesidua', 'presentage' => '89', 'created_at' => $now, 'updated_at' => $now],
            ['name_volume' => 'adzan', 'presentage' => '100', 'created_at' => $now, 'updated_at' => $now],
            ['name_volume' => 'hadist', 'presentage' => '100', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->command->info("Seeding on song_volumes table is succeeded");
    }
}

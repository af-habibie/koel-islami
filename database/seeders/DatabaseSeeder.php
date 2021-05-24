<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $this->call(UserTableSeeder::class);
        $this->call(ArtistTableSeeder::class);
        $this->call(AlbumTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(ListHadist::class);
        $this->call(SongVolumesTableSeeder::class);

        Model::reguard();
    }
}

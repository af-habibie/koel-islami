<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApiLocations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\Models\Location::create([
                    'nama_tempat' => 'Bekasi',
                    'user_id' => 1,
                    'status' => 1
                ]);
    }
}

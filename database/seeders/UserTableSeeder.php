<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        return User::create([
            "name" => "admin",
            "email" => "admin@admin.com",
            "password" => Hash::make("admin123"),
            "is_admin" => true,
        ]);
        User::create($this->getUserDetails() + [
            'is_admin' => true,
        ]);

        if (!app()->runningUnitTests()) {
            $this->command->info('Admin user created. You can (and should) remove the auth details from .env now.');
        }
    }

    protected function getUserDetails()
    {
        $details = array_filter(array_only(config('koel.admin', []), [
            'name', 'email', 'password',
        ]));

        if (count($details) !== 3) {
            $this->command->error('Please fill in initial admin details in .env file first.');

            abort(422);
        }

        $details['password'] = Hash::make($details['password']);

        return $details;
    }
}

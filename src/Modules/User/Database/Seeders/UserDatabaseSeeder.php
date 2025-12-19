<?php

namespace Modules\User\Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create(['password'=>Hash::make('password')]);
        foreach ($users as $user) {
            $user->assignRole('admin');
        }
    }
}

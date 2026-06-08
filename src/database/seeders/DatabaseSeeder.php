<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Sensor\Database\Seeders\SensorDatabaseSeeder;
use Modules\Sensor\Database\Seeders\SensorSeeder;
use Modules\Room\Database\Seeders\RoomDatabaseSeeder;
use Modules\User\Database\Seeders\PermissionDatabaseSeeder;
use Modules\User\Database\Seeders\RoleSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's Database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PermissionDatabaseSeeder::class,
            RoleSeeder::class,
            UserDatabaseSeeder::class,
            SensorDatabaseSeeder::class,
            RoomDatabaseSeeder::class,
            SensorSeeder::class,
        ]);
       
    }
}

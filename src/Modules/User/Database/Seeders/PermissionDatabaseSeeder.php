<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionDatabaseSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'user.create']);
    }
}

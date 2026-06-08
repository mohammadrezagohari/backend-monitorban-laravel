<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $superVisor = Role::firstOrCreate(['name' => 'super-visor', 'guard_name' => 'api']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);

        $all = Permission::where('guard_name', 'api')->pluck('name')->all();

        $superAdmin->syncPermissions($all);
        $admin->syncPermissions([
            'companies.manage',
            'rooms.view',
            'rooms.manage',
            'sensors.view',
            'sensors.manage',
            'sensor-types.manage',
            'units.manage',
            'thresholds.manage',
            'sensor-readings.view',
            'sensor-readings.manage',
            'dashboard.view',
        ]);
        $superVisor->syncPermissions([
            'rooms.view',
            'sensors.view',
            'sensor-readings.view',
            'dashboard.view',
        ]);
        $user->syncPermissions([
            'rooms.view',
            'sensors.view',
            'dashboard.view',
        ]);
    }
}

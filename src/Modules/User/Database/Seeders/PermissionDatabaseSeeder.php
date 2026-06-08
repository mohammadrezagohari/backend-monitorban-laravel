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
        $permissions = [
            'user.create',
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }
    }
}

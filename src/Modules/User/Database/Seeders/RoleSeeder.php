<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\DTO\RoleDTO;
use Modules\User\Models\Role;
;

class RoleSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        
        $superAdmin = RoleDTO::from([
            'name' =>  'super-admin',
        ]);

        $admin = RoleDTO::from([
            'name' => 'admin',
        ]);

        $superVisor = RoleDTO::from([
            'name' => 'super-visor',
        ]);

        Role::create($superAdmin->toArray());
        Role::create($admin->toArray());
        Role::create($superVisor->toArray());
    }
}

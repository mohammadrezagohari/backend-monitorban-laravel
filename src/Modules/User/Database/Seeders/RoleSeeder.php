<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Role\DTO\RoleDTO;
;

class RoleSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        
        $superAdmin = RoleDTO::from([
            'name' => 'سوپر ادمین',
            'slug' => 'super-admin',
        ]);

        $admin = RoleDTO::from([
            'name' => 'ادمین',
            'slug' => 'admin',
        ]);

        $superVisor = RoleDTO::from([
            'name' => 'سوپروایزر',
            'slug' => 'super-visor',
        ]);

        Role::create($superAdmin->toArray());
        Role::create($admin->toArray());
        Role::create($superVisor->toArray());
    }
}

<?php

namespace Modules\User\Database\Seeders;

use App\Models\Company;
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
        Company::updateOrCreate(
            ['slug' => 'monitorban-demo'],
            ['name' => 'Monitorban Demo Company', 'is_active' => true]
        );

        Company::updateOrCreate(
            ['slug' => 'acme-datacenter'],
            ['name' => 'ACME Datacenter', 'is_active' => true]
        );

        $users = [
            [
                'role' => 'super-admin',
                'companies' => [
                    'monitorban-demo' => ['is_owner' => true],
                    'acme-datacenter' => ['is_owner' => true],
                ],
                'data' => [
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'username' => 'superadmin',
                    'email' => 'superadmin@example.com',
                    'mobile' => '09100000001',
                ],
            ],
            [
                'role' => 'admin',
                'companies' => [
                    'monitorban-demo' => ['is_owner' => true],
                ],
                'data' => [
                    'first_name' => 'Company',
                    'last_name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@example.com',
                    'mobile' => '09100000002',
                ],
            ],
            [
                'role' => 'super-visor',
                'companies' => [
                    'monitorban-demo' => ['is_owner' => false],
                ],
                'data' => [
                    'first_name' => 'Room',
                    'last_name' => 'Supervisor',
                    'username' => 'supervisor',
                    'email' => 'supervisor@example.com',
                    'mobile' => '09100000003',
                ],
            ],
            [
                'role' => 'user',
                'companies' => [
                    'monitorban-demo' => ['is_owner' => false],
                ],
                'data' => [
                    'first_name' => 'Dashboard',
                    'last_name' => 'Viewer',
                    'username' => 'viewer',
                    'email' => 'viewer@example.com',
                    'mobile' => '09100000004',
                ],
            ],
        ];

        $companies = Company::whereIn('slug', ['monitorban-demo', 'acme-datacenter'])->get()->keyBy('slug');

        foreach ($users as $item) {
            $user = User::updateOrCreate(
                ['mobile' => $item['data']['mobile']],
                $item['data'] + ['password' => Hash::make('password')]
            );

            $user->syncRoles([$item['role']]);

            foreach ($item['companies'] as $slug => $pivot) {
                $companies[$slug]->users()->syncWithoutDetaching([
                    $user->id => ['is_owner' => $pivot['is_owner']],
                ]);
            }
        }
    }
}

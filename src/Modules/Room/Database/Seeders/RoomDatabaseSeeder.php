<?php

namespace Modules\Room\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Room\Models\ServerRoom;

class RoomDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'monitorban-demo'],
            ['name' => 'Monitorban Demo Company', 'is_active' => true]
        );

        foreach ([
            ['name' => 'Main Server Room', 'location' => 'HQ - Floor 2', 'description' => 'Primary production server room'],
            ['name' => 'Backup Server Room', 'location' => 'HQ - Floor 1', 'description' => 'Backup and disaster recovery room'],
        ] as $room) {
            ServerRoom::updateOrCreate(
                ['company_id' => $company->id, 'name' => $room['name']],
                $room + ['company_id' => $company->id]
            );
        }
    }
}

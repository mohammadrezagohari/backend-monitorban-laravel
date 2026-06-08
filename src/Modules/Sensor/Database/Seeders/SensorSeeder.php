<?php

namespace Modules\Sensor\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Room\Models\ServerRoom;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Models\SensorReading;
use Modules\Sensor\Models\SensorThresholdProfile;
use Modules\Sensor\Models\SensorType;
use Modules\Sensor\Models\Unit;
use Modules\User\Models\Group;
use Modules\User\Models\User;

class SensorSeeder extends Seeder
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

        $rooms = ServerRoom::where('company_id', $company->id)->pluck('id', 'name');
        $types = SensorType::pluck('id', 'key');
        $units = Unit::pluck('id', 'symbol');

        $temperatureProfile = SensorThresholdProfile::updateOrCreate(
            ['company_id' => $company->id, 'sensor_type_id' => $types['temperature'], 'name' => 'Default temperature range'],
            [
                'unit_id' => $units['C'],
                'normal_min' => 10,
                'normal_max' => 15,
                'warning_min' => 7,
                'warning_max' => 18,
                'critical_min' => 5,
                'critical_max' => 21,
            ]
        );

        $humidityProfile = SensorThresholdProfile::updateOrCreate(
            ['company_id' => $company->id, 'sensor_type_id' => $types['humidity'], 'name' => 'Default humidity range'],
            [
                'unit_id' => $units['%'],
                'normal_min' => 35,
                'normal_max' => 55,
                'warning_min' => 25,
                'warning_max' => 65,
                'critical_min' => 15,
                'critical_max' => 75,
            ]
        );

        $sensors = [
            ['room' => 'Main Server Room', 'type' => 'temperature', 'unit' => 'C', 'name' => 'Main temperature 1', 'code' => 'main-temp-01', 'value' => 12.4],
            ['room' => 'Main Server Room', 'type' => 'temperature', 'unit' => 'C', 'name' => 'Main temperature 2', 'code' => 'main-temp-02', 'value' => 16.2],
            ['room' => 'Main Server Room', 'type' => 'humidity', 'unit' => '%', 'name' => 'Main humidity 1', 'code' => 'main-hum-01', 'value' => 45],
            ['room' => 'Main Server Room', 'type' => 'motion', 'unit' => 'bool', 'name' => 'Main motion 1', 'code' => 'main-motion-01', 'boolean' => false],
            ['room' => 'Backup Server Room', 'type' => 'temperature', 'unit' => 'C', 'name' => 'Backup temperature 1', 'code' => 'backup-temp-01', 'value' => 22.5],
            ['room' => 'Backup Server Room', 'type' => 'humidity', 'unit' => '%', 'name' => 'Backup humidity 1', 'code' => 'backup-hum-01', 'value' => 61],
            ['room' => 'Backup Server Room', 'type' => 'fire_suppression', 'unit' => 'bool', 'name' => 'Backup fire suppression', 'code' => 'backup-fire-01', 'boolean' => true],
        ];

        foreach ($sensors as $item) {
            $sensor = Sensor::updateOrCreate(
                ['company_id' => $company->id, 'code' => $item['code']],
                [
                    'company_id' => $company->id,
                    'server_room_id' => $rooms[$item['room']],
                    'sensor_type_id' => $types[$item['type']],
                    'unit_id' => $units[$item['unit']],
                    'name' => $item['name'],
                    'code' => $item['code'],
                    'type' => $item['type'],
                    'title_fa' => $item['name'],
                    'title_en' => $item['name'],
                    'alert_type' => 'threshold',
                    'is_active' => true,
                ]
            );

            if ($item['type'] === 'temperature') {
                $sensor->threshold()->updateOrCreate(
                    ['sensor_id' => $sensor->id],
                    $temperatureProfile->only([
                        'company_id',
                        'unit_id',
                        'normal_min',
                        'normal_max',
                        'warning_min',
                        'warning_max',
                        'critical_min',
                        'critical_max',
                    ])
                );
            }

            if ($item['type'] === 'humidity') {
                $sensor->threshold()->updateOrCreate(
                    ['sensor_id' => $sensor->id],
                    $humidityProfile->only([
                        'company_id',
                        'unit_id',
                        'normal_min',
                        'normal_max',
                        'warning_min',
                        'warning_max',
                        'critical_min',
                        'critical_max',
                    ])
                );
            }

            SensorReading::updateOrCreate(
                ['sensor_id' => $sensor->id, 'recorded_at' => now()->startOfMinute()],
                [
                    'company_id' => $company->id,
                    'unit_id' => $sensor->unit_id,
                    'value_numeric' => $item['value'] ?? null,
                    'value_boolean' => $item['boolean'] ?? null,
                    'recorded_at' => now()->startOfMinute(),
                ]
            );
        }

        $group = Group::updateOrCreate(
            ['slug' => 'main-room-operators'],
            [
                'name' => 'Main Room Operators',
                'description' => 'Can access only main server room sensors.',
            ]
        );

        $mainRoom = ServerRoom::where('company_id', $company->id)->where('name', 'Main Server Room')->first();
        $mainSensors = Sensor::where('company_id', $company->id)
            ->where('server_room_id', $mainRoom->id)
            ->pluck('id')
            ->all();

        $group->serverRooms()->sync([$mainRoom->id]);
        $group->sensors()->sync($mainSensors);

        $supervisor = User::where('mobile', '09100000003')->first();

        if ($supervisor) {
            $supervisor->groups()->syncWithoutDetaching([$group->id]);
        }
    }
}

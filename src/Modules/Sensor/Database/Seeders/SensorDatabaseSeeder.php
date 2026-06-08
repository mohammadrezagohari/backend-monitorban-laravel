<?php

namespace Modules\Sensor\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sensor\Models\SensorType;
use Modules\Sensor\Models\Unit;

class SensorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Temperature', 'key' => 'temperature', 'value_type' => 'number'],
            ['name' => 'Humidity', 'key' => 'humidity', 'value_type' => 'number'],
            ['name' => 'Motion', 'key' => 'motion', 'value_type' => 'boolean'],
            ['name' => 'Fire suppression', 'key' => 'fire_suppression', 'value_type' => 'boolean'],
            ['name' => 'Air quality', 'key' => 'air_quality', 'value_type' => 'number'],
        ] as $type) {
            SensorType::firstOrCreate(['key' => $type['key']], $type);
        }

        foreach ([
            ['name' => 'Celsius', 'symbol' => 'C', 'dimension' => 'temperature', 'is_canonical' => true],
            ['name' => 'Fahrenheit', 'symbol' => 'F', 'dimension' => 'temperature', 'is_canonical' => false],
            ['name' => 'Percent', 'symbol' => '%', 'dimension' => 'ratio', 'is_canonical' => true],
            ['name' => 'Boolean', 'symbol' => 'bool', 'dimension' => 'state', 'is_canonical' => true],
        ] as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol'], 'dimension' => $unit['dimension']], $unit);
        }
    }
}

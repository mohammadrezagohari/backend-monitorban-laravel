<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\CurrentCompany;
use Illuminate\Http\Request;
use Modules\Room\Models\ServerRoom;
use Modules\Sensor\Models\Sensor;

class SensorDashboardController extends Controller
{
    public function summary(Request $request)
    {
        $companyId = CurrentCompany::id($request);

        $sensors = Sensor::with(['serverRoom', 'sensorType', 'unit', 'threshold', 'latestReading'])
            ->where('company_id', $companyId)
            ->when(! $request->user()->hasAnyRole(['super-admin', 'admin']), function ($query) use ($request) {
                $sensorIds = $request->user()->scopedResourceIds(Sensor::class);
                $roomIds = $request->user()->scopedResourceIds(ServerRoom::class);

                $query->where(function ($query) use ($sensorIds, $roomIds) {
                    $query->whereIn('id', $sensorIds)
                        ->orWhereIn('server_room_id', $roomIds);
                });
            })
            ->when($request->filled('server_room_id'), fn ($query) => $query->where('server_room_id', $request->integer('server_room_id')))
            ->get();

        $items = $sensors->map(function (Sensor $sensor) {
            $value = $sensor->latestReading?->value_numeric;
            $status = $this->statusFor($value, $sensor->threshold);

            return [
                'id' => $sensor->id,
                'name' => $sensor->name,
                'server_room' => $sensor->serverRoom?->only(['id', 'name']),
                'sensor_type' => $sensor->sensorType?->only(['id', 'name', 'key']),
                'unit' => $sensor->unit?->only(['id', 'name', 'symbol']),
                'current_value' => $value,
                'status' => $status,
                'recorded_at' => $sensor->latestReading?->recorded_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'sensors' => $items,
                'averages' => $items
                    ->filter(fn ($item) => is_numeric($item['current_value']))
                    ->filter(fn ($item) => $item['server_room'] && $item['sensor_type'])
                    ->groupBy(fn ($item) => $item['server_room']['id'] . ':' . $item['sensor_type']['id'])
                    ->map(function ($group) {
                        $first = $group->first();

                        return [
                            'server_room' => $first['server_room'],
                            'sensor_type' => $first['sensor_type'],
                            'unit' => $first['unit'],
                            'average' => round($group->avg('current_value'), 4),
                            'count' => $group->count(),
                        ];
                    })
                    ->values(),
            ],
        ]);
    }

    private function statusFor(?float $value, $threshold): string
    {
        if ($value === null || ! $threshold) {
            return 'unknown';
        }

        if (
            ($threshold->critical_min !== null && $value < (float) $threshold->critical_min)
            || ($threshold->critical_max !== null && $value > (float) $threshold->critical_max)
        ) {
            return 'critical';
        }

        if (
            ($threshold->warning_min !== null && $value < (float) $threshold->warning_min)
            || ($threshold->warning_max !== null && $value > (float) $threshold->warning_max)
        ) {
            return 'warning';
        }

        return 'normal';
    }
}

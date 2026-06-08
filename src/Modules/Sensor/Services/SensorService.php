<?php

namespace Modules\Sensor\Services;

use App\Support\CurrentCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Room\Models\ServerRoom;
use Modules\Room\Repositories\Contracts\ServerRoomRepositoryInterface;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Repositories\Contracts\SensorRepositoryInterface;
use Modules\Sensor\Repositories\Contracts\SensorTypeRepositoryInterface;

class SensorService
{
    public function __construct(
        private SensorRepositoryInterface $sensors,
        private SensorTypeRepositoryInterface $sensorTypes,
        private ServerRoomRepositoryInterface $rooms,
    ) {
    }

    public function paginateForRequest(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = $this->sensors->query()
            ->with(['serverRoom', 'sensorType', 'unit', 'threshold', 'latestReading'])
            ->where('company_id', CurrentCompany::id($request))
            ->when(! $request->user()->hasAnyRole(['super-admin', 'admin']), function ($query) use ($request) {
                $sensorIds = $request->user()->scopedResourceIds(Sensor::class);
                $roomIds = $request->user()->scopedResourceIds(ServerRoom::class);

                $query->where(function ($query) use ($sensorIds, $roomIds) {
                    $query->whereIn('id', $sensorIds)
                        ->orWhereIn('server_room_id', $roomIds);
                });
            })
            ->when($request->filled('server_room_id'), fn ($query) => $query->where('server_room_id', $request->integer('server_room_id')))
            ->when($request->filled('sensor_type_id'), fn ($query) => $query->where('sensor_type_id', $request->integer('sensor_type_id')));

        return $this->sensors->paginate($query, $perPage);
    }

    public function create(Request $request, array $data): Sensor
    {
        $companyId = CurrentCompany::id($request);
        $threshold = $data['threshold'] ?? null;
        unset($data['threshold']);

        $this->rooms->query()
            ->where('company_id', $companyId)
            ->findOrFail($data['server_room_id']);

        $sensorType = $this->sensorTypes->findOrFail((int) $data['sensor_type_id']);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sensors', 'public');
        }

        $sensor = $this->sensors->create($data + [
            'company_id' => $companyId,
            'type' => $data['type'] ?? $sensorType->key,
            'title_fa' => $data['title_fa'] ?? $data['name'],
            'title_en' => $data['title_en'] ?? $data['name'],
            'alert_type' => $data['alert_type'] ?? 'threshold',
        ]);

        if ($threshold) {
            $sensor->threshold()->create($threshold + [
                'company_id' => $companyId,
                'unit_id' => $threshold['unit_id'] ?? $sensor->unit_id,
            ]);
        }

        return $sensor->load('serverRoom', 'sensorType', 'unit', 'threshold');
    }

    public function findAccessible(Request $request, Sensor $sensor, array $relations = []): Sensor
    {
        abort_unless($sensor->company_id === CurrentCompany::id($request), 404);
        abort_unless($request->user()->canAccessResource($sensor) || $request->user()->canAccessResource($sensor->serverRoom), 404);

        return $sensor->load($relations);
    }

    public function update(Request $request, Sensor $sensor, array $data): Sensor
    {
        $companyId = CurrentCompany::id($request);
        $this->findAccessible($request, $sensor);

        $threshold = $data['threshold'] ?? null;
        unset($data['threshold'], $data['company_id']);

        if (isset($data['server_room_id'])) {
            $this->rooms->query()
                ->where('company_id', $companyId)
                ->findOrFail($data['server_room_id']);
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sensors', 'public');
        }

        $this->sensors->update($sensor, $data);

        if ($threshold) {
            $sensor->threshold()->updateOrCreate(
                ['sensor_id' => $sensor->id],
                $threshold + [
                    'company_id' => $companyId,
                    'unit_id' => $threshold['unit_id'] ?? $sensor->unit_id,
                ]
            );
        }

        return $sensor->load('serverRoom', 'sensorType', 'unit', 'threshold');
    }

    public function delete(Request $request, Sensor $sensor): void
    {
        $this->findAccessible($request, $sensor);
        $this->sensors->delete($sensor);
    }
}

<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Support\CurrentCompany;
use Illuminate\Http\Request;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Models\SensorThresholdProfile;

class SensorThresholdProfileController extends Controller
{
    public function index(Request $request)
    {
        $profiles = SensorThresholdProfile::with('sensorType', 'unit')
            ->where('company_id', CurrentCompany::id($request))
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($profiles);
    }

    public function store(Request $request)
    {
        $companyId = CurrentCompany::id($request);
        $data = $this->validated($request);

        $profile = SensorThresholdProfile::create($data + ['company_id' => $companyId]);

        return response()->json(['status' => 'success', 'data' => $profile->load('sensorType', 'unit')], 201);
    }

    public function show(Request $request, SensorThresholdProfile $thresholdProfile)
    {
        abort_unless($thresholdProfile->company_id === CurrentCompany::id($request), 404);

        return response()->json(['status' => 'success', 'data' => $thresholdProfile->load('sensorType', 'unit')]);
    }

    public function update(Request $request, SensorThresholdProfile $thresholdProfile)
    {
        abort_unless($thresholdProfile->company_id === CurrentCompany::id($request), 404);

        $thresholdProfile->update($this->validated($request, true));

        return response()->json(['status' => 'success', 'data' => $thresholdProfile->load('sensorType', 'unit')]);
    }

    public function destroy(Request $request, SensorThresholdProfile $thresholdProfile)
    {
        abort_unless($thresholdProfile->company_id === CurrentCompany::id($request), 404);
        $thresholdProfile->delete();

        return response()->json(null, 204);
    }

    public function apply(Request $request, SensorThresholdProfile $thresholdProfile)
    {
        $companyId = CurrentCompany::id($request);
        abort_unless($thresholdProfile->company_id === $companyId, 404);

        $data = $request->validate([
            'server_room_id' => ['nullable', 'exists:server_rooms,id'],
        ]);

        $sensors = Sensor::query()
            ->where('company_id', $companyId)
            ->where('sensor_type_id', $thresholdProfile->sensor_type_id)
            ->when(isset($data['server_room_id']), fn ($query) => $query->where('server_room_id', $data['server_room_id']))
            ->get();

        foreach ($sensors as $sensor) {
            $sensor->threshold()->updateOrCreate(
                ['sensor_id' => $sensor->id],
                [
                    'company_id' => $companyId,
                    'unit_id' => $thresholdProfile->unit_id ?? $sensor->unit_id,
                    'normal_min' => $thresholdProfile->normal_min,
                    'normal_max' => $thresholdProfile->normal_max,
                    'warning_min' => $thresholdProfile->warning_min,
                    'warning_max' => $thresholdProfile->warning_max,
                    'critical_min' => $thresholdProfile->critical_min,
                    'critical_max' => $thresholdProfile->critical_max,
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'updated_count' => $sensors->count(),
            ],
        ]);
    }

    private function validated(Request $request, bool $partial = false): array
    {
        $prefix = $partial ? 'sometimes|' : '';

        return $request->validate([
            'sensor_type_id' => [$prefix . 'required', 'exists:sensor_types,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'name' => [$prefix . 'required', 'string', 'max:255'],
            'normal_min' => ['nullable', 'numeric'],
            'normal_max' => ['nullable', 'numeric'],
            'warning_min' => ['nullable', 'numeric'],
            'warning_max' => ['nullable', 'numeric'],
            'critical_min' => ['nullable', 'numeric'],
            'critical_max' => ['nullable', 'numeric'],
        ]);
    }
}

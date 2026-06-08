<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Support\CurrentCompany;
use Illuminate\Http\Request;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Models\SensorReading;

class SensorReadingController extends Controller
{
    public function index(Request $request, Sensor $sensor)
    {
        abort_unless($sensor->company_id === CurrentCompany::id($request), 404);
        abort_unless($request->user()->canAccessResource($sensor) || $request->user()->canAccessResource($sensor->serverRoom), 404);

        $readings = $sensor->readings()
            ->with('unit')
            ->latest('recorded_at')
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($readings);
    }

    public function store(Request $request, Sensor $sensor)
    {
        $companyId = CurrentCompany::id($request);
        abort_unless($sensor->company_id === $companyId, 404);
        abort_unless($request->user()->canAccessResource($sensor) || $request->user()->canAccessResource($sensor->serverRoom), 404);

        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:units,id'],
            'value_numeric' => ['nullable', 'numeric'],
            'value_text' => ['nullable', 'string', 'max:255'],
            'value_boolean' => ['nullable', 'boolean'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        abort_if(
            ! array_key_exists('value_numeric', $data)
            && ! array_key_exists('value_text', $data)
            && ! array_key_exists('value_boolean', $data),
            422,
            'At least one reading value is required.'
        );

        $reading = SensorReading::create($data + [
            'company_id' => $companyId,
            'sensor_id' => $sensor->id,
            'unit_id' => $data['unit_id'] ?? $sensor->unit_id,
            'recorded_at' => $data['recorded_at'] ?? now(),
        ]);

        return response()->json(['status' => 'success', 'data' => $reading->load('unit')], 201);
    }
}

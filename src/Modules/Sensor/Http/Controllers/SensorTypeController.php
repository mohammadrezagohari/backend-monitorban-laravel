<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Sensor\Models\SensorType;

class SensorTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = SensorType::query()
            ->latest()
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($types);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:sensor_types,key'],
            'value_type' => ['required', Rule::in(['number', 'boolean', 'text'])],
        ]);

        return response()->json(['status' => 'success', 'data' => SensorType::create($data)], 201);
    }

    public function show(SensorType $sensorType)
    {
        return response()->json(['status' => 'success', 'data' => $sensorType]);
    }

    public function update(Request $request, SensorType $sensorType)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'key' => ['sometimes', 'required', 'string', 'max:100', 'alpha_dash', Rule::unique('sensor_types', 'key')->ignore($sensorType->id)],
            'value_type' => ['sometimes', 'required', Rule::in(['number', 'boolean', 'text'])],
        ]);

        $sensorType->update($data);

        return response()->json(['status' => 'success', 'data' => $sensorType]);
    }

    public function destroy(SensorType $sensorType)
    {
        $sensorType->delete();

        return response()->json(null, 204);
    }
}

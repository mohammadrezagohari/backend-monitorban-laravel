<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\Sensor\Http\Requests\StoreSensorRequest;
use Modules\Sensor\Http\Requests\UpdateSensorRequest;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Data\SensorData;
use Modules\Sensor\Data\StoreSensorData;
use Modules\Sensor\Data\UpdateSensorData;
use OpenApi\Attributes as OA;

class SensorController extends Controller
{
    #[OA\Get(
        path: "/api/v1/sensors",
        summary: "List sensors",
        security: [["bearerAuth" => []]],
        tags: ["Sensors"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of sensors",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: SensorData::class))
            )
        ]
    )]
    public function index(Request $request)
    {
        $sensors = Sensor::with(['serverRoom'])
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($sensors);
    }

    #[OA\Post(
        path: "/api/v1/sensors",
        summary: "Create sensor",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: StoreSensorData::class)
        ),
        tags: ["Sensors"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Sensor created",
                content: new OA\JsonContent(ref: SensorData::class)
            )
        ]
    )]
    public function store(StoreSensorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sensors', 'public');
        }

        $sensor = Sensor::create($data);
        return response()->json($sensor, 201);
    }

    #[OA\Get(
        path: "/api/v1/sensors/{sensor}",
        summary: "Show sensor",
        security: [["bearerAuth" => []]],
        tags: ["Sensors"],
        parameters: [
            new OA\Parameter(name: "sensor", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sensor details",
                content: new OA\JsonContent(ref: SensorData::class)
            )
        ]
    )]
    public function show(Sensor $sensor)
    {
        return $sensor;
    }

    #[OA\Put(
        path: "/api/v1/sensors/{sensor}",
        summary: "Update sensor",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "sensor", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: UpdateSensorData::class)
        ),
        tags: ["Sensors"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sensor updated",
                content: new OA\JsonContent(ref: SensorData::class)
            )
        ]
    )]
    public function update(UpdateSensorRequest $request, Sensor $sensor)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sensors', 'public');
        }

        $sensor->update($data);
        return response()->json($sensor);
    }

    #[OA\Delete(
        path: "/api/v1/sensors/{sensor}",
        summary: "Delete sensor",
        security: [["bearerAuth" => []]],
        tags: ["Sensors"],
        parameters: [
            new OA\Parameter(name: "sensor", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Deleted")
        ]
    )]
    public function destroy(Sensor $sensor)
    {
        $sensor->delete();
        return response()->json(null, 204);
    }
}

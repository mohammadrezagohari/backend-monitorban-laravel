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
use Modules\Sensor\Services\SensorService;
use OpenApi\Attributes as OA;

class SensorController extends Controller
{
    public function __construct(private SensorService $sensors)
    {
    }

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
        return ApiResponse::paginated(
            $this->sensors->paginateForRequest($request, ApiResponse::perPage($request->query('per_page')))
        );
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
        return response()->json(['status' => 'success', 'data' => $this->sensors->create($request, $request->validated())], 201);
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
        return response()->json([
            'status' => 'success',
            'data' => $this->sensors->findAccessible(request(), $sensor, ['serverRoom', 'sensorType', 'unit', 'threshold', 'latestReading']),
        ]);
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
        return response()->json(['status' => 'success', 'data' => $this->sensors->update($request, $sensor, $request->validated())]);
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
        $this->sensors->delete(request(), $sensor);

        return response()->json(null, 204);
    }
}

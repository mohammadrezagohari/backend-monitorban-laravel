<?php

namespace Modules\Sensor\Http\Controllers;

use App\Http\Controllers\Controller;
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
        path: "/api/sensors",
        summary: "List sensors",
        tags: ["Sensors"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of sensors",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/SensorData"))
            )
        ]
    )]
    public function index()
    {
        return Sensor::all();
    }

    #[OA\Post(
        path: "/api/sensors",
        summary: "Create sensor",
        tags: ["Sensors"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreSensorData")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Sensor created",
                content: new OA\JsonContent(ref: "#/components/schemas/SensorData")
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
        path: "/api/sensors/{id}",
        summary: "Show sensor",
        tags: ["Sensors"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sensor details",
                content: new OA\JsonContent(ref: "#/components/schemas/SensorData")
            )
        ]
    )]
    public function show(Sensor $sensor)
    {
        return $sensor;
    }

    #[OA\Put(
        path: "/api/sensors/{id}",
        summary: "Update sensor",
        tags: ["Sensors"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateSensorData")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Sensor updated",
                content: new OA\JsonContent(ref: "#/components/schemas/SensorData")
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
        path: "/api/sensors/{id}",
        summary: "Delete sensor",
        tags: ["Sensors"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true)
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

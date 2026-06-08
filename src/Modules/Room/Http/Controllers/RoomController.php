<?php

namespace Modules\Room\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\Room\Services\RoomService;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Rooms",
    description: "Endpoints for managing server rooms"
)]
class RoomController extends Controller
{
    public function __construct(private RoomService $rooms)
    {
    }

    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/rooms",
        summary: "List server rooms",
        security: [["bearerAuth" => []]],
        tags: ["Rooms"],
        responses: [
            new OA\Response(response: 200, description: "Server rooms list"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request)
    {
        return ApiResponse::paginated(
            $this->rooms->paginateForRequest($request, ApiResponse::perPage($request->query('per_page')))
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('room::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: "/api/v1/rooms",
        summary: "Create a server room",
        security: [["bearerAuth" => []]],
        tags: ["Rooms"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "company_id", type: "integer", example: 1, nullable: true),
                    new OA\Property(property: "name", type: "string", example: "Main server room"),
                    new OA\Property(property: "location", type: "string", example: "Floor 2"),
                    new OA\Property(property: "description", type: "string", example: "Primary server room"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Server room created"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Forbidden or user is not assigned to the requested company"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => $this->rooms->create($request)], 201);
    }

    /**
     * Show the specified resource.
     */
    #[OA\Get(
        path: "/api/v1/rooms/{room}",
        summary: "Show a server room",
        security: [["bearerAuth" => []]],
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(name: "room", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Server room details"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Server room not found"),
        ]
    )]
    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->rooms->findAccessible(request(), (int) $id, ['sensors.sensorType', 'sensors.unit']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('room::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: "/api/v1/rooms/{room}",
        summary: "Update a server room",
        security: [["bearerAuth" => []]],
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(name: "room", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Main server room"),
                    new OA\Property(property: "location", type: "string", example: "Floor 2"),
                    new OA\Property(property: "description", type: "string", example: "Primary server room"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Server room updated"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Server room not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(Request $request, $id)
    {
        return response()->json(['status' => 'success', 'data' => $this->rooms->update($request, (int) $id)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/v1/rooms/{room}",
        summary: "Delete a server room",
        security: [["bearerAuth" => []]],
        tags: ["Rooms"],
        parameters: [
            new OA\Parameter(name: "room", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 204, description: "Server room deleted"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Server room not found"),
        ]
    )]
    public function destroy($id)
    {
        $this->rooms->delete(request(), (int) $id);

        return response()->json(null, 204);
    }
}

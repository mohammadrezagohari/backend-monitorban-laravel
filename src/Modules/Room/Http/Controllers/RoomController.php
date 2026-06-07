<?php

namespace Modules\Room\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\Room\Models\ServerRoom;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Rooms",
    description: "Endpoints for managing server rooms"
)]
class RoomController extends Controller
{
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
        $rooms = ServerRoom::query()
            ->latest()
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($rooms);
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
                    new OA\Property(property: "name", type: "string", example: "Main server room"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Server room created"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request) {}

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
        return view('room::show');
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
    public function update(Request $request, $id) {}

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
    public function destroy($id) {}
}

<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Modules\User\Models\User;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Users",
    description: "API Endpoints for managing users"
)]
class UserController extends Controller
{
    #[OA\Get(
        path: "/api/v1/users",
        summary: "Get list of users",
        security: [["bearerAuth" => []]],
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation"
            )
        ]
    )]
    public function index(Request $request)
    {
        $users = User::with(['roles', 'groups', 'companies'])
            ->paginate(ApiResponse::perPage($request->query('per_page')));

        return ApiResponse::paginated($users);
    }

    #[OA\Post(
        path: "/api/v1/users",
        summary: "Create a new user",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "mobile", "password"],
                properties: [
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "mobile", type: "string", example: "09123456789"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully"
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile' => $validated['mobile'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    #[OA\Get(
        path: "/api/v1/users/{user}",
        summary: "Get a user by ID",
        security: [["bearerAuth" => []]],
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: "user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation"
            )
        ]
    )]
    public function show($id)
    {
        $user = User::with(['roles', 'groups', 'companies'])->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $user]);
    }

    #[OA\Put(
        path: "/api/v1/users/{user}",
        tags: ["Users"],
        summary: "Update a user",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated successfully"
            )
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    #[OA\Delete(
        path: "/api/v1/users/{user}",
        tags: ["Users"],
        summary: "Delete a user",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "User deleted successfully"
            )
        ]
    )]
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}

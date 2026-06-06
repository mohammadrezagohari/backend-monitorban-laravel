<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Permission;

#[OA\Tag(
    name: "Permissions",
    description: "Admin endpoints for managing permissions"
)]
class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            "name" => "nullable|string",
            "guard_name" => "nullable|string",
            "page" => "nullable|numeric",
            "per_page" => "nullable|numeric"
        ]);

        $perPage = $data["per_page"] ?? 10;
        $page = $data["page"] ?? 1;

        $permissions = Permission::paginate(perPage: $perPage, page: $page);
        return response()->json(['data' => $permissions], 200);
    }

    #[OA\Post(
        path: "/api/v1/permissions",
        summary: "Create a permission",
        security: [["bearerAuth" => []]],
        tags: ["Permissions"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "view dashboard"),
                    new OA\Property(property: "guard_name", type: "string", example: "web", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Permission created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:permissions,name',
            'guard_name' => 'nullable|string|max:50',
        ]);

        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string|max:50',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $data['name'];
        $permission->guard_name = $data['guard_name'];
        $permission->save();

        return response()->json([
            'message' => '',
            'data' => $permission
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'آیتم با موفقیت حذف گرید'], 200);
    }
}

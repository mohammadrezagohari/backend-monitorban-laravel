<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\DTO\RoleValidationDTO;
use Modules\User\SwaggerDTO\Role\RoleRequestDTO;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Roles",
    description: "Admin endpoints for managing roles"
)]
class RoleController extends Controller
{

    #[OA\Get(
        path: "/api/v1/roles",
        summary: "List roles",
        security: [["bearerAuth" => []]],
        tags: ["Roles"],
        parameters: [
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Roles list"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Forbidden"),
        ]
    )]
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

        $permissions = Role::with(relations: ['permissions'])->paginate(perPage: $perPage, page: $page);
        return response()->json(['data' => $permissions], 200);
    }


    #[OA\Post(
        path: "/api/v1/roles",
        summary: "Create a new role",
        security: [["bearerAuth" => []]],
        tags: ["Roles"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Role creation payload",
            content: new OA\JsonContent(ref: RoleRequestDTO::class)
        ),
        responses: [
            new OA\Response(response: 201, description: "Role created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $dto = RoleValidationDTO::validateAndCreate(payload: $request);

        $role = $dto->toModel();
        $role->save();

        $dto->syncPermissions($role);

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions')
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'name' => $data['name'],
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json([
            'message' => 'بروزرسانی انجام شده است',
            'data' => $role->load('permissions')
        ], 200);
    }


    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions([]);
        $role->delete();

        return response()->json(['message' => 'آیتم با موفقیت حذف گرید'], 200);
    }
}

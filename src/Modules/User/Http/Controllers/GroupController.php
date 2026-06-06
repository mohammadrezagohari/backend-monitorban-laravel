<?php
namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\Group; // مدل Group که ساختیم
use Spatie\Permission\Models\Permission;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

#[OA\Tag(
    name: "Group Management",
    description: "Endpoints for managing groups and their permissions"
)]
class GroupController extends Controller
{
     /**
     * Create a new group
     */
    #[OA\Post(
        path: "/api/v1/groups",
        summary: "Create a new group",
        requestBody: new OA\RequestBody(
            description: "Details required to create a new group",
            required: true,
            content: new OA\JsonContent(
                required: ["name", "slug"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Administrators"),
                    new OA\Property(property: "slug", type: "string", example: "administrators"),
                    new OA\Property(property: "description", type: "string", example: "Group with full access to all resources"),
                    new OA\Property(
                        property: "permissions",
                        description: "Array of permission IDs to attach to the group"
                    ),
                ]
            )
        ),
        tags: ["Group Management"],
        responses: [
            new OA\Response(response: HTTPResponse::HTTP_CREATED, description: "Group created successfully"),
            new OA\Response(response: HTTPResponse::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:groups,name',
            'slug' => 'required|string|max:150|unique:groups,slug',
            'description' => 'nullable|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $group = Group::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            $group->permissions()->sync($data['permissions']);
        }

        return response()->json([
            'message' => 'Group created successfully',
            'data' => $group->load('permissions')
        ], 201);
    }


        /**
     * Get a group by ID
     */
    #[OA\Get(
        path: "/api/v1/groups/{id}",
        summary: "Retrieve a group by ID",
        tags: ["Group Management"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the group to retrieve",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: HTTPResponse::HTTP_OK,
                description: "Group retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "slug", type: "string"),
                        new OA\Property(property: "description", type: "string", nullable: true),
                        new OA\Property(
                            property: "permissions",
                        ),
                    ]
                )
            ),
            new OA\Response(response: HTTPResponse::HTTP_NOT_FOUND, description: "Group not found"),
        ]
    )]

    public function show($id)
    {
        $group = Group::with('permissions')->findOrFail($id);

        return response()->json($group);
    }


    /**
     * Update permissions for a group
     */
    #[OA\Put(
        path: "/api/v1/groups/{id}/permissions",
        summary: "Update permissions of a specific group",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Permissions to assign to the group",
            content: new OA\JsonContent(
                required: ["permissions"],
                properties: [
                    new OA\Property(
                        property: "permissions",
                        description: "Array of permission IDs"
                    ),
                ]
            )
        ),
        tags: ["Group Management"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID of the group to update"
            )
        ],
        responses: [
            new OA\Response(response: HTTPResponse::HTTP_OK, description: "Permissions updated successfully"),
            new OA\Response(response: HTTPResponse::HTTP_NOT_FOUND, description: "Group not found"),
            new OA\Response(response: HTTPResponse::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error"),
        ]
    )]
    public function updatePermissions(Request $request, $id)
    {
        $data = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $group = Group::findOrFail($id);
        $group->permissions()->sync($data['permissions']);

        return response()->json([
            'message' => 'Group permissions updated successfully',
            'data' => $group->load('permissions')
        ]);
    }
}

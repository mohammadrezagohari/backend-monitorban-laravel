<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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

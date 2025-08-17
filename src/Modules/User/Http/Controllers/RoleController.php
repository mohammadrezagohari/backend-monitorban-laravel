<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
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

        $permissions = Role::with(relations: ['permissions'])->paginate(perPage: $perPage, page: $page);
        return response()->json(['data' => $permissions], 200);
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
        }

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions')
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $data['name'];
        $role->save();

        if (!empty($data['permissions'])) {
            $role->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
        }

        return response()->json([
            'message' => 'بروزرسانی انجام شده است',
            'data' => $role
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

<?php
namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authorization\Models\Group; // مدل Group که ساختیم
use Spatie\Permission\Models\Permission;

class GroupController extends Controller
{
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

    public function show($id)
    {
        $group = Group::with('permissions')->findOrFail($id);

        return response()->json($group);
    }

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

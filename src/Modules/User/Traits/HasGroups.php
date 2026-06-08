<?php

namespace Modules\User\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\Group;
use Spatie\Permission\Models\Permission;

trait HasGroups
{
    public function groups()
    {
        return $this->morphToMany(Group::class, 'model', 'model_has_groups', 'model_id', 'group_id');
    }

    public function permissionsViaGroups()
    {
        return Permission::query()
            ->select('permissions.*')
            ->join('group_has_permissions', 'permissions.id', '=', 'group_has_permissions.permission_id')
            ->join('model_has_groups', 'group_has_permissions.group_id', '=', 'model_has_groups.group_id')
            ->where('model_has_groups.model_type', static::class)
            ->where('model_has_groups.model_id', $this->getKey())
            ->groupBy('permissions.id')
            ->get();
    }

    public function hasPermissionViaGroups(string|Permission $permission): bool
    {
        $permissionName = $permission instanceof Permission ? $permission->name : $permission;
        return $this->permissionsViaGroups()->contains(fn ($p) => $p->name === $permissionName);
    }

    public function canAccessResource(Model $resource): bool
    {
        if (method_exists($this, 'hasAnyRole') && $this->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return DB::table('group_resource_access')
            ->join('model_has_groups', 'group_resource_access.group_id', '=', 'model_has_groups.group_id')
            ->where('model_has_groups.model_type', static::class)
            ->where('model_has_groups.model_id', $this->getKey())
            ->where('group_resource_access.resource_type', $resource::class)
            ->where('group_resource_access.resource_id', $resource->getKey())
            ->exists();
    }

    public function scopedResourceIds(string $resourceClass): array
    {
        if (method_exists($this, 'hasAnyRole') && $this->hasAnyRole(['super-admin', 'admin'])) {
            return [];
        }

        return $this->groups()
            ->join('group_resource_access', 'groups.id', '=', 'group_resource_access.group_id')
            ->where('group_resource_access.resource_type', $resourceClass)
            ->pluck('group_resource_access.resource_id')
            ->unique()
            ->values()
            ->all();
    }
}

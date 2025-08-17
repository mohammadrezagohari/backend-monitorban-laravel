<?php

namespace Modules\User\Traits;

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
}

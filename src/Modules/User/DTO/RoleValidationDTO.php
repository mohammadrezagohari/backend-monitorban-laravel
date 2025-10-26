<?php

namespace Modules\User\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Modules\User\Models\Role as RoleModel;

class RoleValidationDTO extends Data
{
    public string $name;
    public ?string $guard_name = 'web';
    public array $permissions = [];

    public static function rules(ValidationContext $context = null): array
    {
        return [
            'name'        => ['required', 'string', 'max:150', 'unique:roles,name'],
            'guard_name'  => ['nullable', 'string', 'max:50'],
            'permissions' => ['array', 'sometimes'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function toModel(): RoleModel
    {
        return new RoleModel([
            'name'       => $this->name,
            'guard_name' => $this->guard_name ?? 'web',
        ]);
    }

    public function syncPermissions(RoleModel $role): void
    {
        if (!empty($this->permissions)) {
            $role->permissions()->sync($this->permissions);
        }
    }
}

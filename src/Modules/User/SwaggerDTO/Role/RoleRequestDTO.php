<?php

namespace Modules\User\SwaggerDTO\Role;

use Spatie\LaravelData\Data;
use OpenApi\Attributes as OA;
use Modules\User\Models\Role as RoleModel;

#[OA\Schema(
    schema: "RoleRequestDTO",
    required: ["name"],
    description: "Payload for creating or updating a Role"
)]
class RoleRequestDTO extends Data
{
    #[OA\Property(type: 'string', example: 'admin')]
    public string $name;

    #[OA\Property(type: 'string', example: 'web')]
    public ?string $guard_name = 'web';

    #[OA\Property(
        type: 'array',
        items: new OA\Items(type: 'integer'),
        example: [1, 2]
    )]
    public array $permissions = [];

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

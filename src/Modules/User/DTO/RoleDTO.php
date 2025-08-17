<?php

namespace Modules\Role\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;

class RoleDTO extends Data
{
    #[Rule('required|string|unique:roles,name')]
    public string $name;

    #[Rule('required|string|unique:roles,slug')]
    public string $slug;
}

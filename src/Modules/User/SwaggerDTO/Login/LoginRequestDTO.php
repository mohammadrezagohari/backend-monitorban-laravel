<?php

namespace Modules\User\SwaggerDTO\Login;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "LoginRequestDTO")]
class LoginRequestDTO
{
    #[OA\Property(type: 'string', example: '09123456789')]
    public string $mobile;

    #[OA\Property(type: 'string', format: 'password', example: 'password123')]
    public string $password;
}

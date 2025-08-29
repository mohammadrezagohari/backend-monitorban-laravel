<?php

namespace Modules\User\SwaggerDTO\User;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "UserRequestDTO")]
class UserRequestDTO
{
    #[OA\Property(type: 'string', example: 'محمدرضا')]
    public string $first_name;

    #[OA\Property(type: 'string', example: 'گوهری')]
    public string $last_name;

    #[OA\Property(type: 'string', example: 'gohari')]
    public string $username;

    #[OA\Property(type: 'string', format: 'email', example: 'example@gandom.link')]
    public string $email;

    #[OA\Property(type: 'string', format: 'password', example: 'password123')]
    public string $password;

    #[OA\Property(type: 'string', example: '09123456789')]
    public ?string $mobile;
}

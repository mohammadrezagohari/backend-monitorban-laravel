<?php

namespace Modules\User\SwaggerDTO\User;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "UserResponseDTO")]
class UserResponseDTO
{
    #[OA\Property(type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(type: 'string', example: 'محمدرضا')]
    public string $first_name;

    #[OA\Property(type: 'string', example: 'گوهری')]
    public string $last_name;

    #[OA\Property(type: 'string', example: 'gohari')]
    public string $username;

    #[OA\Property(type: 'string', format: 'email', example: 'example@gandom.link')]
    public string $email;

    #[OA\Property(type: 'string', example: '09123456789')]
    public ?string $mobile;

    #[OA\Property(type: 'string', format: 'date-time', example: '2025-08-27 21:42:00')]
    public string $created_at;

    #[OA\Property(type: 'string', format: 'date-time', example: '2025-08-27 21:42:00')]
    public string $updated_at;
}

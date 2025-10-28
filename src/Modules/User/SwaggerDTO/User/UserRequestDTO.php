<?php

namespace Modules\User\SwaggerDTO\User;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[OA\Schema(schema: "UserRequestDTO")]
class UserRequestDTO extends Data
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

    public static function rules(ValidationContext $context = null): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:150'],
            'last_name' => ['required', 'string', 'min:2', 'max:150'],
            'username' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['nullable', 'string', 'max:300'],
            'mobile' => ['required', 'string', 'min:5', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}



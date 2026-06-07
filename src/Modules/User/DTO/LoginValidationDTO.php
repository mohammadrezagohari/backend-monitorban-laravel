<?php

namespace Modules\User\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "LoginRequestDTO",
    required: ["mobile", "password"],
    type: "object"
)]
class LoginValidationDTO extends Data
{
    #[OA\Property(type: 'string', example: '09123456789')]
    public string $mobile;
    #[OA\Property(type: 'string', format: 'password', example: 'password123')]
    public string $password;

    public static function rules(ValidationContext $context = null): array
    {
        return [
            'mobile' => ['required', 'string', 'min:9', 'max:15'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}

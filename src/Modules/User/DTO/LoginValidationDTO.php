<?php

namespace Modules\User\DTO\Login;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class LoginValidationDTO extends Data
{
    public string $mobile;
    public string $password;

    public static function rules(ValidationContext $context = null) : array
    {
        return [
            'mobile' => ['required', 'string', 'min:9', 'max:15'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}

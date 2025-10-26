<?php

namespace Modules\User\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Modules\User\Models\User;

class UserValidationDTO extends Data
{
    public string $first_name;
    public string $last_name;
    public string $username;
    public string $email;
    public string $password;
    public ?string $mobile;

    public static function rules(ValidationContext $context = null): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
            'username'   => ['required', 'string', 'unique:users,username'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'mobile'     => ['nullable', 'string', 'min:9', 'max:15'],
        ];
    }

    public function toModel(): User
    {
        return new User([
            ...$this->except('password')->toArray(),
            'password' => bcrypt($this->password),
        ]);
    }
}

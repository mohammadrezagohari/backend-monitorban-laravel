<?php

namespace Modules\User\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class UserDTO extends Data
{
    #[Rule('required|string')]
    public string $first_name;

    #[Rule('required|string')]
    public string $last_name;

    #[Rule('required|string|unique:users')]
    public string $username;

    #[Rule('required|email|unique:users')]
    public string $email;

    #[Rule('required|string|min:6')]
    public string $password;

    #[Rule('nullable|string|min:9|max:15')]
    public ?string $mobile;

    #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d h:i:s')]
    #[Rule('nullable|date')]
    public ?\DateTime $created_at;

    #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d h:i:s')]
    #[Rule('nullable|date')]
    public ?\DateTime $updated_at;

    // // اگر لازم باشه relation (مثلاً ID) رو ولیدیت کنیم
    // #[Rule('exists:roles,id')]
    // public ?int $role_id = null;

    public function toModel(): \Modules\User\Models\User
    {
        return new \Modules\User\Models\User([
            ...$this->except('password')->toArray(),
            'password' => bcrypt($this->password),
        ]);
    }
}

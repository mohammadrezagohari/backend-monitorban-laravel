<?php

namespace Modules\User\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\User\Database\Factories\UserFactoryFactory;
use Modules\User\Traits\HasGroups;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles , HasGroups , HasFactory;

    protected static function newFactory(): UserFactoryFactory
    {
        return UserFactoryFactory::new();
    }
    protected $hidden=[
        'password',
    ];

    protected $fillable = ['first_name', 'last_name', 'username', 'email', 'password', 'mobile'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

  public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id'    );
    }


    public function groups()
    {
        return $this->morphToMany(Group::class, 'model', 'model_has_groups', 'model_id', 'group_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withPivot('is_owner')->withTimestamps();
    }

    public function primaryCompany(): ?Company
    {
        return $this->companies()->first();
    }
}

<?php

namespace Modules\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasRoles , HasGroups;

    protected $fillable = ['first_name', 'last_name', 'username', 'email', 'password', 'mobile'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function roles()
    {
        return $this->belongsToMany(\Modules\Role\Models\Role::class);
    }


    public function groups()
    {
        return $this->morphToMany(Group::class, 'model', 'model_has_groups', 'model_id', 'group_id');
    }

}

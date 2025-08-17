<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\User;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}

<?php
namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Group extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_has_permissions');
    }

    public function users()
    {
        // اگر خواستی محدود به User کنی:
        return $this->morphedByMany(User::class, 'model', 'model_has_groups');
    }
}

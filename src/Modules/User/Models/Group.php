<?php
namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Room\Models\ServerRoom;
use Modules\Sensor\Models\Sensor;
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

    public function serverRooms()
    {
        return $this->morphedByMany(ServerRoom::class, 'resource', 'group_resource_access');
    }

    public function sensors()
    {
        return $this->morphedByMany(Sensor::class, 'resource', 'group_resource_access');
    }
}

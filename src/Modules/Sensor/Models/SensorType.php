<?php

namespace Modules\Sensor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SensorType extends Model
{
    protected $fillable = ['name', 'key', 'value_type'];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }
}

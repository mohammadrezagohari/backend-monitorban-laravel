<?php

namespace Modules\Sensor\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'symbol', 'dimension', 'is_canonical'];

    protected $casts = [
        'is_canonical' => 'boolean',
    ];
}

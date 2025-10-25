<?php

namespace Modules\Sensor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sensor\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): SensorFactory
    // {
    //     // return SensorFactory::new();
    // }
}

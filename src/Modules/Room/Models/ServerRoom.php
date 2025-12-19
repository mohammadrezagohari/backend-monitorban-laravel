<?php

namespace Modules\Room\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Room\Database\Factories\ServerRoomFactory;

class ServerRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ServerRoomFactory
    // {
    //     // return ServerRoomFactory::new();
    // }
}

<?php

namespace Modules\Room\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sensor\Models\Sensor;
// use Modules\Room\Database\Factories\ServerRoomFactory;

class ServerRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['company_id', 'name', 'location', 'description'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    // protected static function newFactory(): ServerRoomFactory
    // {
    //     // return ServerRoomFactory::new();
    // }
}

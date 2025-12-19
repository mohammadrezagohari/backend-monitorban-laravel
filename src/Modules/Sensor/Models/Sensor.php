<?php

namespace Modules\Sensor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Sensor\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'server_room_id',
        'type',
        'title_fa',
        'title_en',
        'alert_type',
        'physical_address',
        'unit',
        'alert_interval',
        'alert_count',
        'min_daily_record',
        'recordable_changes',
        'has_critical_history',
        'has_warning_history',
        'crisis_committee',
        'icon',
        'profile_picture',
    ];

    public function serverRoom()
    {
        return $this->belongsTo(ServerRoom::class);
    }
}

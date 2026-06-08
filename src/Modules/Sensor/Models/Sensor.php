<?php

namespace Modules\Sensor\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Room\Models\ServerRoom;
// use Modules\Sensor\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'server_room_id',
        'sensor_type_id',
        'unit_id',
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
        'is_active',
    ];

    protected $casts = [
        'has_critical_history' => 'boolean',
        'has_warning_history' => 'boolean',
        'crisis_committee' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function serverRoom(): BelongsTo
    {
        return $this->belongsTo(ServerRoom::class);
    }

    public function sensorType(): BelongsTo
    {
        return $this->belongsTo(SensorType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function threshold(): HasOne
    {
        return $this->hasOne(SensorThreshold::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(SensorReading::class)->latestOfMany('recorded_at');
    }
}

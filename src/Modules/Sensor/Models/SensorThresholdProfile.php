<?php

namespace Modules\Sensor\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorThresholdProfile extends Model
{
    protected $fillable = [
        'company_id',
        'sensor_type_id',
        'unit_id',
        'name',
        'normal_min',
        'normal_max',
        'warning_min',
        'warning_max',
        'critical_min',
        'critical_max',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sensorType(): BelongsTo
    {
        return $this->belongsTo(SensorType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}

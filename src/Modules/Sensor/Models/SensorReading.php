<?php

namespace Modules\Sensor\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $fillable = [
        'company_id',
        'sensor_id',
        'unit_id',
        'value_numeric',
        'value_text',
        'value_boolean',
        'recorded_at',
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}

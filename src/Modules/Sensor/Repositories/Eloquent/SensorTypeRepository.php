<?php

namespace Modules\Sensor\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use Modules\Sensor\Models\SensorType;
use Modules\Sensor\Repositories\Contracts\SensorTypeRepositoryInterface;

class SensorTypeRepository extends BaseRepository implements SensorTypeRepositoryInterface
{
    public function __construct(SensorType $sensorType)
    {
        parent::__construct($sensorType);
    }
}

<?php

namespace Modules\Sensor\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use Modules\Sensor\Models\Sensor;
use Modules\Sensor\Repositories\Contracts\SensorRepositoryInterface;

class SensorRepository extends BaseRepository implements SensorRepositoryInterface
{
    public function __construct(Sensor $sensor)
    {
        parent::__construct($sensor);
    }
}

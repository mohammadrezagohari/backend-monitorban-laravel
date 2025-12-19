<?php

namespace Modules\Sensor\Data;

use Modules\Sensor\Data\StoreSensorData;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateSensorData",
    type: "object"
)]
class UpdateSensorData extends StoreSensorData
{
}


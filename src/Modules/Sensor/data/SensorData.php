<?php

namespace Modules\Sensor\Data;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;

#[OA\Schema(
    schema: "SensorData",
    description: "Installed sensor resource",
    type: "object"
)]
class SensorData extends Data
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id,

        #[OA\Property(example: 1)]
        public int $company_id,

        #[OA\Property(example: "Temperature Sensor 1")]
        public string $name,

        #[OA\Property(example: "temp-a-01")]
        public ?string $code,

        #[OA\Property(example: 1)]
        public int $server_room_id,

        #[OA\Property(example: 1)]
        public int $sensor_type_id,

        #[OA\Property(example: 1)]
        public ?int $unit_id,

        #[OA\Property(example: true)]
        public bool $is_active,
    ) {}
}

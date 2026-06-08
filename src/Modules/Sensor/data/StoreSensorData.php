<?php

namespace Modules\Sensor\Data;

use OpenApi\Attributes as OA;
use Spatie\LaravelData\Data;

#[OA\Schema(
    schema: "StoreSensorData",
    required: ["name", "server_room_id", "sensor_type_id"],
    type: "object"
)]
class StoreSensorData extends Data
{
    public function __construct(
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

        #[OA\Property(example: "0x01")]
        public ?string $physical_address,

        #[OA\Property(example: true)]
        public ?bool $is_active,

        #[OA\Property(
            properties: [
                new OA\Property(property: "normal_min", type: "number", example: 10),
                new OA\Property(property: "normal_max", type: "number", example: 15),
                new OA\Property(property: "warning_min", type: "number", example: 7),
                new OA\Property(property: "warning_max", type: "number", example: 18),
                new OA\Property(property: "critical_min", type: "number", example: 5),
                new OA\Property(property: "critical_max", type: "number", example: 21),
            ],
            type: "object",
            nullable: true
        )]
        public ?array $threshold,
    ) {}
}

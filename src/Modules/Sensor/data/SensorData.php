<?php
namespace Modules\Sensor\Data;

use Spatie\LaravelData\Data;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "SensorData",
    description: "Sensor resource",
    type: "object"
)]
class SensorData extends Data
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id,

        #[OA\Property(example: "Temperature Sensor")]
        public string $name,

        #[OA\Property(example: 3)]
        public int $server_room_id,

        #[OA\Property(example: "temperature")]
        public string $type,

        #[OA\Property(example: "سنسور دما")]
        public string $title_fa,

        #[OA\Property(example: "Temperature Sensor")]
        public string $title_en,

        #[OA\Property(example: "warning")]
        public string $alert_type,

        #[OA\Property(example: "0x01")]
        public ?string $physical_address,

        #[OA\Property(example: "°C")]
        public ?string $unit,

        #[OA\Property(example: 10)]
        public ?int $alert_interval,

        #[OA\Property(example: 3)]
        public ?int $alert_count,

        #[OA\Property(example: 50)]
        public ?int $min_daily_record,

        #[OA\Property(example: "1-degree change")]
        public ?string $recordable_changes,

        #[OA\Property(example: true)]
        public bool $has_critical_history,

        #[OA\Property(example: false)]
        public bool $has_warning_history,

        #[OA\Property(example: false)]
        public bool $crisis_committee,

        #[OA\Property(example: "icon.png")]
        public ?string $icon,

        #[OA\Property(example: "profile.jpg")]
        public ?string $profile_picture,
    ) {}
}

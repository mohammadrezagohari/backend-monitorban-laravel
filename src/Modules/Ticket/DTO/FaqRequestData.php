<?php

namespace Modules\Ticket\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;


class FaqRequestData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $question,

        #[Required, StringType]
        public string $answer,

        #[BooleanType]
        public bool $is_active = true,

        #[IntegerType]
        public int $sort_order = 0
    ) {}
}

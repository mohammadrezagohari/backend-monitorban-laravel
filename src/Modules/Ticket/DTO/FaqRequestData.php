<?php

namespace Modules\Ticket\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "FaqRequestData",
    title: "FAQ Request",
    description: "FAQ request body data",
    required: ["question", "answer"],
    type: "object"
)]
class FaqRequestData extends Data
{
    public function __construct(
        #[OA\Property(example: "How do I reset my password?")]
        #[Required, StringType]
        public string $question,

        #[OA\Property(example: "Use the password reset option on the login page.")]
        #[Required, StringType]
        public string $answer,

        #[OA\Property(example: true)]
        #[BooleanType]
        public bool $is_active = true,

        #[OA\Property(example: 1)]
        #[IntegerType]
        public int $sort_order = 0

    ) {}
}

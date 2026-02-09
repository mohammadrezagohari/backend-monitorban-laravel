<?php

namespace Modules\Ticket\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;

class TicketRequestData extends Data
{
    public function __construct(
        #[Required, StringType, Min(3)]
        public string  $subject,

        #[Required, StringType]
        public string  $recipient,

        #[Required, StringType, Min(10)]
        public string  $message,

        // Status is optional during update, forbidden during create usually,
        // but we allow it here for the 'Update' CRUD action.
        #[In(['open', 'closed', 'pending'])]
        public ?string $status = null
    )
    {
    }
}

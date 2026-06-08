<?php

namespace Modules\Ticket\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;
use OpenApi\Attributes as OA;
#[OA\Schema(
    title: "Ticket Request",
    description: "Ticket request body data",
    required: ["subject", "recipient", "message"]
)]
class TicketRequestData extends Data
{
    public function __construct(
        #[OA\Property(title: "Subject", example: "Login Error")]
        #[Required, StringType, Min(3)]
        public string  $subject,

        #[OA\Property(title: "Recipient", example: "technical")]
        #[Required, StringType]
        public string  $recipient,

        #[OA\Property(title: "Message", example: "I cannot login to the dashboard.")]
        #[Required, StringType, Min(10)]
        public string  $message,

        // Status is optional during update, forbidden during create usually,
        // but we allow it here for the 'Update' CRUD action.
        #[OA\Property(title: "Status", description: "Only for updates", enum: ["open", "closed"], example: "open")]
        #[In(['open', 'closed', 'pending'])]
        public ?string $status = null
    )
    {
    }
}

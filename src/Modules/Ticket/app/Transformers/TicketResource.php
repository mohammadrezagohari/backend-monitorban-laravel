<?php

namespace Modules\Ticket\app\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'recipient' => $this->recipient,
            'message' => $this->message, // In a real app, you might truncate this in a list view
            'status' => $this->status,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

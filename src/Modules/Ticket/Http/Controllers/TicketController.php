<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ticket\app\Transformers\TicketResource;
use Modules\Ticket\DTO\TicketRequestData;
use Modules\Ticket\Models\Ticket;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        // Pagination is crucial for APIs
        $tickets = Ticket::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => TicketResource::collection($tickets)->response()->getData(true)
        ]);
    }

    /**
     * CREATE: Store a new ticket.
     * Spatie Data automatically validates the request before entering the method.
     */
    public function store(TicketRequestData $data): JsonResponse
    {
        $ticket = Ticket::create([
            'user_id'   => auth()->id(),
            'subject'   => $data->subject,
            'recipient' => $data->recipient,
            'message'   => $data->message,
            'status'    => 'open',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully.',
            'data' => new TicketResource($ticket)
        ], 201);
    }

    /**
     * SHOW: Get a single ticket details.
     */
    public function show($id): JsonResponse
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new TicketResource($ticket)
        ]);
    }

    /**
     * UPDATE: Update an existing ticket.
     */
    public function update(TicketRequestData $data, $id): JsonResponse
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        // Only update fields that are present in the request or necessary
        $ticket->update([
            'subject' => $data->subject,
            'recipient' => $data->recipient,
            'message' => $data->message,
            // Allow status update if provided, else keep existing
            'status' => $data->status ?? $ticket->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket updated successfully.',
            'data' => new TicketResource($ticket)
        ]);
    }

    /**
     * DELETE: Remove a ticket.
     */
    public function destroy($id): JsonResponse
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        $ticket->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket deleted successfully.'
        ]);
    }
}

<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ticket\app\Transformers\TicketResource;
use Modules\Ticket\DTO\TicketRequestData;
use Modules\Ticket\Models\Ticket;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Tickets",
    description: "Authenticated endpoints for support tickets"
)]
class TicketController extends Controller
{
    #[OA\Get(
        path: "/api/v1/tickets",
        summary: "List current user's tickets",
        security: [["bearerAuth" => []]],
        tags: ["Tickets"],
        responses: [
            new OA\Response(response: 200, description: "Tickets list"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
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
    #[OA\Post(
        path: "/api/v1/tickets",
        summary: "Create a ticket",
        security: [["bearerAuth" => []]],
        tags: ["Tickets"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: TicketRequestData::class)
        ),
        responses: [
            new OA\Response(response: 201, description: "Ticket created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
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
    #[OA\Get(
        path: "/api/v1/tickets/{ticket}",
        summary: "Show a ticket",
        security: [["bearerAuth" => []]],
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Ticket details"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Ticket not found"),
        ]
    )]
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
    #[OA\Put(
        path: "/api/v1/tickets/{ticket}",
        summary: "Update a ticket",
        security: [["bearerAuth" => []]],
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: TicketRequestData::class)
        ),
        responses: [
            new OA\Response(response: 200, description: "Ticket updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Ticket not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
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
    #[OA\Delete(
        path: "/api/v1/tickets/{ticket}",
        summary: "Delete a ticket",
        security: [["bearerAuth" => []]],
        tags: ["Tickets"],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Ticket deleted successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Ticket not found"),
        ]
    )]
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

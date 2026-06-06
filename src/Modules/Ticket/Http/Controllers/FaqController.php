<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ticket\app\Transformers\FaqResource;
use Modules\Ticket\Models\Faq;
use Modules\Ticket\DTO\FaqRequestData;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "FAQ",
    description: "Endpoints for frequently asked questions"
)]
class FaqController extends Controller
{
    /**
     * READ (List): Get all FAQs.
     * Users usually only see active ones, Admins see all.
     */
    #[OA\Get(
        path: "/api/faq/list",
        summary: "List public FAQs",
        tags: ["FAQ"],
        responses: [
            new OA\Response(response: 200, description: "FAQ list"),
        ]
    )]
    #[OA\Get(
        path: "/api/v1/faq",
        summary: "List FAQs",
        security: [["bearerAuth" => []]],
        tags: ["FAQ"],
        responses: [
            new OA\Response(response: 200, description: "FAQ list"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(): JsonResponse
    {
        // Simple logic: If user is logged in, show all?
        // Or just return all for the management table.
        $faqs = Faq::orderBy('sort_order', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => FaqResource::collection($faqs)
        ]);
    }

    /**
     * CREATE: Add a new FAQ.
     */
    #[OA\Post(
        path: "/api/v1/faq/store",
        summary: "Create FAQ",
        security: [["bearerAuth" => []]],
        tags: ["FAQ"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: FaqRequestData::class)
        ),
        responses: [
            new OA\Response(response: 201, description: "FAQ created successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function store(FaqRequestData $data): JsonResponse
    {
        $faq = Faq::create([
            'question' => $data->question,
            'answer' => $data->answer,
            'is_active' => $data->is_active,
            'sort_order' => $data->sort_order,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ created successfully.',
            'data' => new FaqResource($faq->load('user'))
        ], 201);
    }

    /**
     * SHOW: Get a single FAQ details.
     */
    #[OA\Get(
        path: "/api/faq/{id}",
        summary: "Show public FAQ",
        tags: ["FAQ"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "FAQ details"),
            new OA\Response(response: 404, description: "FAQ not found"),
        ]
    )]
    #[OA\Get(
        path: "/api/v1/faq/{id}",
        summary: "Show FAQ",
        security: [["bearerAuth" => []]],
        tags: ["FAQ"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "FAQ details"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "FAQ not found"),
        ]
    )]
    public function show($id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new FaqResource($faq)
        ]);
    }

    /**
     * UPDATE: Edit an FAQ.
     */
    #[OA\Put(
        path: "/api/v1/faq/{id}",
        summary: "Update FAQ",
        security: [["bearerAuth" => []]],
        tags: ["FAQ"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: FaqRequestData::class)
        ),
        responses: [
            new OA\Response(response: 200, description: "FAQ updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "FAQ not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(FaqRequestData $data, $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        $faq->update([
            'question' => $data->question,
            'answer' => $data->answer,
            'is_active' => $data->is_active,
            'sort_order' => $data->sort_order,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ updated successfully.',
            'data' => new FaqResource($faq)
        ]);
    }

    /**
     * DELETE: Remove an FAQ.
     */
    #[OA\Delete(
        path: "/api/v1/faq/{id}",
        summary: "Delete FAQ",
        security: [["bearerAuth" => []]],
        tags: ["FAQ"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "FAQ deleted successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "FAQ not found"),
        ]
    )]
    public function destroy($id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ deleted successfully.'
        ]);
    }
}

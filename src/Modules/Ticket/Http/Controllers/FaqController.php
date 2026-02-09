<?php

namespace Modules\Ticket\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Ticket\app\Transformers\FaqResource;
use Modules\Ticket\Models\Faq;
use Modules\Ticket\DTO\FaqRequestData;

class FaqController extends Controller
{
    /**
     * READ (List): Get all FAQs.
     * Users usually only see active ones, Admins see all.
     */
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
            'data' => new FaqResource($faq)
        ], 201);
    }

    /**
     * SHOW: Get a single FAQ details.
     */
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

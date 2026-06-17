<?php

namespace Tests\Unit;

use App\Support\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_per_page_uses_default_when_value_is_missing(): void
    {
        $this->assertSame(10, ApiResponse::perPage(null));
        $this->assertSame(25, ApiResponse::perPage('', 25));
    }

    public function test_per_page_uses_default_for_zero_and_never_goes_below_one(): void
    {
        $this->assertSame(10, ApiResponse::perPage(0));
        $this->assertSame(1, ApiResponse::perPage(-5));
    }

    public function test_per_page_is_capped_by_maximum(): void
    {
        $this->assertSame(100, ApiResponse::perPage(250));
        $this->assertSame(50, ApiResponse::perPage(75, default: 10, max: 50));
    }

    public function test_paginated_response_uses_consistent_payload_shape(): void
    {
        $paginator = new LengthAwarePaginator(
            items: [['id' => 1], ['id' => 2]],
            total: 7,
            perPage: 2,
            currentPage: 2,
            options: ['path' => '/api/v1/sensors']
        );

        $payload = ApiResponse::paginated($paginator)->getData(true);

        $this->assertSame('success', $payload['status']);
        $this->assertSame([['id' => 1], ['id' => 2]], $payload['data']);
        $this->assertSame([
            'current_page' => 2,
            'per_page' => 2,
            'from' => 3,
            'to' => 4,
            'total' => 7,
            'last_page' => 4,
            'has_more_pages' => true,
        ], $payload['meta']);
        $this->assertSame('/api/v1/sensors?page=1', $payload['links']['first']);
        $this->assertSame('/api/v1/sensors?page=4', $payload['links']['last']);
        $this->assertSame('/api/v1/sensors?page=1', $payload['links']['prev']);
        $this->assertSame('/api/v1/sensors?page=3', $payload['links']['next']);
    }
}

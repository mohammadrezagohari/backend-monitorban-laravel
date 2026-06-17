<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_public_auth_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes())->map->uri();

        $this->assertContains('api/v1/auth/register', $routes);
        $this->assertContains('api/v1/auth/login', $routes);
        $this->assertContains('api/v1/auth/refresh-token', $routes);
        $this->assertContains('api/v1/auth/request-otp', $routes);
        $this->assertContains('api/v1/auth/verify-otp', $routes);
    }

    public function test_company_and_access_control_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes())->map->uri();

        $this->assertContains('api/v1/companies', $routes);
        $this->assertContains('api/v1/companies/{company}/users', $routes);
        $this->assertContains('api/v1/groups/{id}/permissions', $routes);
        $this->assertContains('api/v1/groups/{id}/resource-access', $routes);
    }

    public function test_monitoring_domain_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes())->map->uri();

        $this->assertContains('api/v1/rooms', $routes);
        $this->assertContains('api/v1/sensors/dashboard/summary', $routes);
        $this->assertContains('api/v1/sensors/{sensor}/readings', $routes);
        $this->assertContains('api/v1/threshold-profiles/{threshold_profile}/apply', $routes);
    }
}

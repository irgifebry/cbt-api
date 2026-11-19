<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_health_endpoint_returns_stable_json_contract(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertExactJson([
                'success' => true,
                'service' => 'cbt-api',
            ]);
    }

    public function test_no_web_or_storage_file_routes_are_exposed(): void
    {
        $this->get('/')->assertNotFound();

        $routes = app('router')->getRoutes();

        foreach ($routes as $route) {
            $this->assertNotSame('storage/{path}', $route->uri());
        }
    }
}

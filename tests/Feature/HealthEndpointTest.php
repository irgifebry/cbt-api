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
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Test the API foundation features, including:
 * - Health endpoint
 * - JSON error responses for unknown routes
 * - Authentication requirements for protected routes
 */
class ApiFoundationTest extends TestCase
{
    /**
     * Test the health endpoint returns a standard success JSON response.
     */
    public function test_health_endpoint_returns_standard_success_json(): void
    {
        $response = $this->getJson('/api/v1/health');
        $response->assertOk()->assertJsonPath('success', true) ->assertJsonPath('data.status', 'ok')->assertHeader('X-Request-Id');
    }
           
    /**
     * Test that an unknown route returns a JSON 404 response.
     */
    public function test_unknown_route_returns_json_404(): void
    {
        $this->getJson('/api/v1/does-not-exist')->assertStatus(404)->assertJsonPath('success', false);    
    }

    /**
     * Test that a protected route requires authentication and returns a JSON 401 response if not authenticated.
     */
    public function test_protected_route_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me') ->assertStatus(401)->assertJsonPath('success', false);    
    }
}
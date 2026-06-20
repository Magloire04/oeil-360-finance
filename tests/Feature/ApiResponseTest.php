<?php

namespace Tests\Feature;

use App\Http\Responses\ApiResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response_has_correct_envelope_structure(): void
    {
        $response = ApiResponse::success(['id' => 1], ['total' => 1], 200);
        $payload  = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['id' => 1], $payload['data']);
        $this->assertSame(['total' => 1], $payload['meta']);
        $this->assertNull($payload['error']);
    }

    public function test_error_response_has_correct_envelope_structure(): void
    {
        $response = ApiResponse::error('Ressource introuvable', 'NOT_FOUND', null, 404);
        $payload  = $response->getData(true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertNull($payload['data']);
        $this->assertNull($payload['meta']);
        $this->assertSame('Ressource introuvable', $payload['error']['message']);
        $this->assertSame('NOT_FOUND', $payload['error']['code']);
        $this->assertNull($payload['error']['details']);
    }

    public function test_not_found_api_route_returns_json_envelope(): void
    {
        $response = $this->getJson('/api/nonexistent-route-xyz');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'data',
            'meta',
            'error' => ['message', 'code', 'details'],
        ]);
        $response->assertJson([
            'data' => null,
            'meta' => null,
            'error' => [
                'code' => 'NOT_FOUND',
            ],
        ]);
    }
}

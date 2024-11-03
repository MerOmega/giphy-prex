<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\ApiCallTest;

class SearchByIdTest extends ApiCallTest
{
    public function test_get_gif_by_id_success()
    {
        // Mock a successful response from Giphy API
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'id' => '1',
                    'title' => 'Funny Cat GIF',
                    'url' => 'https://giphy.com/gifs/cat1',
                ]
            ], 200)
        ]);

        $response = $this->getJson('/api/gifs/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'url']
            ]);
    }

    public function test_get_gif_by_id_not_found()
    {
        // Mock a 404 response from Giphy API
        Http::fake([
            '*' => Http::response(null, 404)
        ]);

        $response = $this->getJson('/api/gifs/invalid-id');

        $response->assertStatus(404)
            ->assertJson(['error' => 'GIF not found']);
    }

    public function test_get_gif_by_id_with_invalid_format()
    {
        // Testing for invalid ID format (e.g., special characters)
        $response = $this->getJson('/api/gifs/!@#$%');

        // Assuming validation on the ID format if needed, otherwise it will make an API call
        $response->assertStatus(404)
            ->assertJson(['error' => 'GIF not found']);
    }
}

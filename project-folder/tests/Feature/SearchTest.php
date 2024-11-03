<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\ApiCallTest;

class SearchTest extends ApiCallTest
{
    public function test_search_gifs_with_valid_query()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    ['id' => '1', 'title' => 'Funny Cat GIF', 'url' => 'https://giphy.com/gifs/cat1'],
                    ['id' => '2', 'title' => 'Dancing Dog GIF', 'url' => 'https://giphy.com/gifs/dog2'],
                ],
                'pagination' => ['total_count' => 100, 'count' => 2, 'offset' => 0]
            ], 200)
        ]);

        $response = $this->getJson('/api/gifs/search?query=funny');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'url']
                ],
                'pagination' => ['total_count', 'count', 'offset']
            ]);
    }

    public function test_search_gifs_with_missing_query_parameter()
    {
        $response = $this->getJson('/api/gifs/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_search_gifs_with_optional_parameters()
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    ['id' => '1', 'title' => 'Funny Cat GIF', 'url' => 'https://giphy.com/gifs/cat1'],
                ],
                'pagination' => ['total_count' => 100, 'count' => 1, 'offset' => 5]
            ])
        ]);

        $response = $this->getJson('/api/gifs/search?query=funny&limit=1&offset=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'url']
                ],
                'pagination' => ['total_count', 'count', 'offset']
            ]);
    }

    public function test_search_gifs_with_invalid_limit_and_offset()
    {
        $response = $this->getJson('/api/gifs/search?query=funny&limit=abc&offset=-10');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit', 'offset']);
    }
}

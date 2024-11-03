<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\ApiCallTest;

class SaveFavoriteGifTest extends ApiCallTest
{
    public function test_save_favorite_gif_successful()
    {
        $response = $this->postJson('/api/gifs/favorite', [
            'gif_id' => '1',
            'alias' => 'Funny Cat GIF',
            'user_id' => User::where('email', 'testuser@example.com')->first()->id,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'GIF saved as favorite']);

        $this->assertDatabaseHas('favorite_gifs', [
            'gif_id' => '1',
            'alias' => 'Funny Cat GIF',
            'user_id' => User::where('email', 'testuser@example.com')->first()->id,
        ]);
    }

    public function test_save_favorite_gif_with_missing_fields()
    {
        $response = $this->postJson('/api/gifs/favorite', [
            'gif_id' => '1',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['alias', 'user_id']);
    }

    public function test_save_favorite_gif_user_not_found()
    {
        $response = $this->postJson('/api/gifs/favorite', [
            'gif_id' => '1',
            'alias' => 'Funny Cat GIF',
            'user_id' => 999,
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'User not found']);
    }
}

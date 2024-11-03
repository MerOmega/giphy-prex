<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FavoriteGif>
 */
class FavoriteGifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gif_id' => $this->faker->unique()->randomNumber(),
            'alias' => $this->faker->unique()->word(),
            'user_id' => User::factory()->create()->id,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\CommunityPost;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunityPost>
 */
class CommunityPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'zone_id' => Zone::query()->inRandomOrder()->value('id'),
            'category' => 'UPDATE',
            'title' => fake()->sentence(5),
            'body' => fake()->paragraph(),
            'status' => 'PUBLISHED',
        ];
    }
}

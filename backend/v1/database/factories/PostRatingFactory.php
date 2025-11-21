<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostRating>
 */
class PostRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'profile_type' => $this->faker->randomElement(['h', 'd', 'm', 'ph', 'l', 'c', 'pa']),
            'profile_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'value' => $this->faker->boolean(70) ? $this->faker->boolean() : null
        ];
    }
}

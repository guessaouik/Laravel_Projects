<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ArticleRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'profile_type' => $this->faker->randomElement(['h', 'c', 'm', 'l', 'd', 'ph', 'pa']),
            'profile_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'value' => $this->faker->boolean(70) ? $this->faker->boolean() : null
        ];
    }
}

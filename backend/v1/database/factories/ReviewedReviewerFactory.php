<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewedReviewer>
 */
class ReviewedReviewerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "reviewed_type" => $this->faker->randomElement(['h', 'c', 'm', 'l', 'd', 'ph']),
            "reviewed_id" => rand(1, TABLE_SAMPLE_NUMBER),
            'reviewer_type' => $this->faker->randomElement(['h', 'c', 'm', 'l', 'd', 'ph', 'pa']),
            'reviewer_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'review_id' => rand(1, TABLE_SAMPLE_NUMBER)
        ];
    }
}

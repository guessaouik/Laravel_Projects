<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfileView>
 */
class ProfileViewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_type' => $this->faker->randomElement(['d', 'pa']),
            'profile_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'specialty_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'views' => rand(1, 100)
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalOffice>
 */
class MedicalOfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'longitude' => $this->faker->randomFloat(4, 1, 200),
            'latitude' => $this->faker->randomFloat(4, 1, 200),
            'address' => $this->faker->address()
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(
            FactoryHelper::person(),
            [
                'birth_date' => $this->faker->date(),
                'gender' => $this->faker->randomElement(['f', 'm']),
                'blood_type' => $this->faker->randomElement(
                    ['a+', 'a-', 'b+', 'b-', 'o+', 'o-', 'ab+', 'ab-']
                )
            ]
        );
    }
}

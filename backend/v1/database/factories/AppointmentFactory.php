<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_id' => rand(1, SECONDARY_TABLE_SAMPLE_NUMBER),
            'patient_id' => rand(1, TABLE_SAMPLE_NUMBER),
            'info' => $this->faker->text(),
            'status' => $this->faker->boolean() ? $this->faker->boolean() : null,
            'session_number' => rand(1, 8),
        ];
    }
}

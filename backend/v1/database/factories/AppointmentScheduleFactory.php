<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppointmentSchedule>
 */
class AppointmentScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "doctor_id" => rand(1, TABLE_SAMPLE_NUMBER),
            "date" => fake()->unique()->date(),
            "interval" => FactoryHelper::getDaySchedule(1),
            "appointments_number" => rand(1, 8),
        ];
    }
}

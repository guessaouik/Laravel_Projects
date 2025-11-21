<?php

namespace Database\Seeders;

use App\Models\AppointmentSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentSchedule::factory(SECONDARY_TABLE_SAMPLE_NUMBER)->create();
    }
}

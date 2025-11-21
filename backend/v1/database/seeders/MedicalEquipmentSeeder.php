<?php

namespace Database\Seeders;

use App\Models\MedicalEquipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicalEquipment::factory()->count(TABLE_SAMPLE_NUMBER)->create();
    }
}

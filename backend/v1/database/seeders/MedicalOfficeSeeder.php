<?php

namespace Database\Seeders;

use App\Models\MedicalOffice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedicalOffice::factory()->count(TABLE_SAMPLE_NUMBER)->create();
    }
}

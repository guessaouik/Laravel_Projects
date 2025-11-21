<?php

namespace Database\Seeders;

use App\Models\MIC;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MICSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("m_i_c_s", "MIC");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PivotSeeder extends Seeder
{
    private const PIVOTS = [
        ["lab_test", "lab", "test"],
        ["equipment_pharmacy", "equipment", "pharmacy"],
        ["mic_technology", "mic", "technology"]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (static::PIVOTS as $pivotInfo){
            SeederHelper::seedPivot($pivotInfo[0], $pivotInfo[1], $pivotInfo[2]);
        }
    }
}

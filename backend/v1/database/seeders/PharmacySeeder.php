<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Error;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    private static function recursiveImplode(string $separator, array $arr){
        return "[" . implode($separator, array_map(
            fn($value) => is_array($value) ? static::recursiveImplode($separator, $value) : $value,
            $arr
        )) . "]";
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("pharmacies", "Pharmacy");
    }
}
<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Error;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederHelper extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void{}

    public static function seedSimpleMorphPivot(
        string $tableName,
        array $aliases, 
        string $singularPrefix,
        string $morphedPrefix,
        int $numSamples = SECONDARY_TABLE_SAMPLE_NUMBER,
        int $singularNumRecords = TABLE_SAMPLE_NUMBER,
        int $morphedNumRecords = TABLE_SAMPLE_NUMBER
    ){
        $result = [];
        for ($i = 0; $i < $numSamples; $i++){
            $result[] = [
                $singularPrefix === "" ? "id" : $singularPrefix . "_id" => rand(1, $singularNumRecords),
                $morphedPrefix.'_type' => $aliases[array_rand($aliases)],
                $morphedPrefix.'_id' => rand(1, $morphedNumRecords)
            ];
        }
        DB::table($tableName)->insert($result);
    }

    public static function seedComplexMorphPivot(
        string $tableName,
        string $prefix1,
        string $prefix2,
        array $aliases1,
        array $aliases2
    ){
        $result = [];
        for ($i = 0; $i < SECONDARY_TABLE_SAMPLE_NUMBER; $i++){
            $result[] = [
                $prefix1.'_type' => $aliases1[array_rand($aliases1)],
                $prefix1.'_id' => rand(1, TABLE_SAMPLE_NUMBER),
                $prefix2.'_type' => $aliases2[array_rand($aliases2)],
                $prefix2.'_id' => rand(1, TABLE_SAMPLE_NUMBER)
            ];
        }
        DB::table($tableName)->insert($result);
    }

    public static function seedUsingFactory(string $tableName, string $model, int $numberOfRecords = TABLE_SAMPLE_NUMBER){
        $factory = "Database\\Factories\\$model" . 'Factory';
        $factory = new $factory();
        $result = [];
        for ($i = 0; $i < $numberOfRecords; $i++){
            $result[] = $factory->definition();
        }
        DB::table($tableName)->insert($result);
    }

    public static function seedPivot(string $tableName, string $prefix1, string $prefix2){
        $result = [];
        for ($i = 0; $i < SECONDARY_TABLE_SAMPLE_NUMBER; $i++){
            $result[] = [
                $prefix1."_id" => rand(1, TABLE_SAMPLE_NUMBER),
                $prefix2."_id" => rand(1, TABLE_SAMPLE_NUMBER)
            ];
        }
        DB::table($tableName)->insert($result);
    }

    public static function seedFilter(string $filter, string $tableName){
        $file = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . "photos" . DIRECTORY_SEPARATOR . "Filters" . DIRECTORY_SEPARATOR . $filter . ".txt", "r");
        $items = fgetcsv($file);
        foreach ($items as $item){
            DB::table($tableName)->insert(["name" => $item]);
        }
    }

    public static function seedStates(){
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "photos" . DIRECTORY_SEPARATOR . "Filters" . DIRECTORY_SEPARATOR . "State.txt";
        $states = json_decode(file_get_contents($file));
        foreach ($states as $state){
            $model = State::create(["name" => $state->french]);
            foreach ($state->Dairas as $daira){
                foreach ($daira->Baladiyas as $baladia){
                    City::create([
                        "state_id" => $model->getKey(),
                        "name" => $baladia->french
                    ]);
                }
            }
        }
    }
}

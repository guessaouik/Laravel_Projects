<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private const EXCLUDES = [
        'DatabaseSeeder', "SeederHelper"
    ];
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $leaveLast = [
            "MorphSeeder", "PivotSeeder", "HospitalSeeder", "ClinicSeeder",
            "CitySeeder", "AppointmentSeeder", "ReviewedReviewerSeeder",
            "ProfileViewSeeder", "ArticleRatingSeeder", "ReviewRatingSeeder",
            "PostRatingSeeder", "AppointmentScheduleSeeder",
        ];
        $seeders = [];
        
        foreach (scandir(__DIR__) as $file){
            $file = explode(".", $file)[0];
            if (!in_array($file, static::EXCLUDES) && $file !== "" && !in_array($file, $leaveLast)){

                $seeders[] = "\\Database\\Seeders\\" . (explode(".", $file)[0]);
            }
        }

        $filters = [
            ["Disease", "diseases"],
            ["Equipment", "medical_equipments"],
            ["Privilege", "privileges"],
            ["Specialty", "specialties"],
            ["Technology", "technologies"],
            ["Test", "tests"],
            ["Treatment", "treatments"]
        ];
        foreach ($filters as $filter){
            SeederHelper::seedFilter($filter[0], $filter[1]);
        }
        SeederHelper::seedStates();

        foreach(array_reverse($leaveLast) as $model){
            $seeders[] = "\\Database\\Seeders\\$model";
        }
        
        $this->call($seeders);
    }
}

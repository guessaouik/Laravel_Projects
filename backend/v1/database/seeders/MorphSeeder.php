<?php

namespace Database\Seeders;

use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MorphSeeder extends Seeder
{
    // table name, singular prefix, morphed prefix, aliases, number of records in table,
    // number of records in singular table, number of records in morphed tables
    private const SIMPLE_MORPH_PIVOTS = [
        ['article_provider', 'article', 'provider', ['h', "d", "m", "ph", "l", "c"]],
        ['post_profile', 'post', 'profile', ['h', "d", "m", "ph", "l", "c", "pa"]],
        ['component_specialty', 'specialty', 'component', ['h', "d", "po", "a", "c"]],
        ['privilege_profile', 'privilege', 'profile', ['h', "d", "m", "ph", "l", "c"]],
        ['profile_user', 'user', 'profile', ['h', "d", "m", "ph", "l", "c", "pa"]],
        ['disease_provider', 'disease', 'provider', ['h', "d", "ph", "c"]],
        ['provider_treatment', 'treatment', 'provider', ['h', "d", "ph", "c"]],
        ["doctor_institution", "doctor", "institution", ['h', "o", "c"]],
        ['city_provider', 'city', 'provider', ['h', "d", "m", "ph", "l", "c", "o"]],
        ['provider_service', 'service', 'provider', ['h', "c"]],
        ['notification_profile', 'notification', 'profile', ['h', 'c', 'm', 'l', 'd', 'ph', 'pa'], NOTIFICATION_SAMPLE_NUMBER, NOTIFICATION_SAMPLE_NUMBER],
        ['provider_schedule', "schedule", "provider", ['c', 'd', 'l', 'm'], SCHEDULE_SAMPLE_NUMBER, SCHEDULE_SAMPLE_NUMBER]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (static::SIMPLE_MORPH_PIVOTS as $pivotInfo){
            SeederHelper::seedSimpleMorphPivot(
                $pivotInfo[0], 
                $pivotInfo[3],
                $pivotInfo[1],
                $pivotInfo[2],
                $pivotInfo[4] ?? SECONDARY_TABLE_SAMPLE_NUMBER,
                $pivotInfo[5] ?? TABLE_SAMPLE_NUMBER,
                $pivotInfo[6] ?? TABLE_SAMPLE_NUMBER
            );
        }
    }
}

<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use Relationship\Morph;

trait HasSavedProfiles{

    private static array $savedModelAlias = [
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Clinic" => "c",
        "App\\Models\\MIC" => "m",
        "App\\Models\\Lab" => "l",
        'App\\Models\\Doctor' => 'd',
        'App\\Models\\Pharmacy' => 'ph'
    ];

    private static array $saverModelAlias = [
        "App\\Models\\Patient" => "pa",
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Clinic" => "c",
        "App\\Models\\MIC" => "m",
        "App\\Models\\Lab" => "l",
        'App\\Models\\Doctor' => 'd',
        'App\\Models\\Pharmacy' => 'ph'
    ];



    public function savedProfiles(){
        return Morph::hasManyThroughManyMorphs(
            $this,
            "saved_profiles",
            "saver_type",
            "saver_id",
            "saved_type",
            "saved_id",
            static::$saverModelAlias[$this::class],
            static::$savedModelAlias
        );
    }
}
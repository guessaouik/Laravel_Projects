<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use Relationship\Morph;

trait HasReviewers{

    private static array $reviewedQualifiedModelAliases = [
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Clinic" => "c",
        "App\\Models\\MIC" => "m",
        "App\\Models\\Lab" => "l",
        'App\\Models\\Doctor' => 'd',
        'App\\Models\\Pharmacy' => 'ph'
    ];

    private static array $reviewerModelAliases = [
        "App\\Models\\Patient" => "pa",
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Clinic" => "c",
        "App\\Models\\MIC" => "m",
        "App\\Models\\Lab" => "l",
        'App\\Models\\Doctor' => 'd',
        'App\\Models\\Pharmacy' => 'ph'
    ];



    public function reviewers(){
        return Morph::hasManyThroughManyMorphs(
            $this,
            "reviewed_reviewer",
            "reviewed_type",
            "reviewed_id",
            "reviewer_type",
            "reviewer_id",
            static::$reviewedQualifiedModelAliases[$this::class],
            static::$reviewerModelAliases
        );
    }
}
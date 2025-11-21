<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use Relationship\Morph;

trait HasReviewed{

    private static array $reviewerQualifiedModelAlias = [
        "App\\Models\\Hospital" => "h",
        "App\\Models\\Clinic" => "c",
        "App\\Models\\MIC" => "m",
        "App\\Models\\Lab" => "l",
        "App\\Models\\Doctor" => "d",
        "App\\Models\\Pharmacy" => "ph",
        "App\\Models\\Patient" => "pa"
    ];

    private static array $reviewedModelAliases = [
        "Hospital" => "h",
        "Clinic" => "c",
        "MIC" => "m",
        "Lab" => "l",
        'Doctor' => 'd',
        'Pharmacy' => 'ph'
    ];

    public function reviewed(){
        return Morph::hasManyThroughManyMorphs(
            $this,
            'reviewed_reviewer',
            'reviewer_type',
            'reviewer_id',
            'reviewed_type',
            'reviewed_id',
            static::$reviewerQualifiedModelAlias[$this::class],
            static::$reviewedModelAliases
        );
    }
}
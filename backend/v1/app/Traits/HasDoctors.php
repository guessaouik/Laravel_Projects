<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Doctor;
use Relationship\Morph;

trait HasDoctors{

    public function doctors(){
        return Morph::hasManyThroughMorph(
            $this,
            Doctor::class,
            "doctor_institution",
            "institution_type",
            "institution_id",
            "doctor_id"
        );
    }
}
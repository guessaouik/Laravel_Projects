<?php

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Specialty;
use Relationship\Morph;

trait HasSpecialties{

    public function specialties(){
        return Morph::hasManyThroughMorph(
            $this,
            Specialty::class,
            'component_specialty',
            'component_type',
            'component_id',
            'specialty_id'
        );
    }
}
<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Treatment;
use Relationship\Morph;

trait HasTreatments{
    
    public function treatments(){
        return Morph::hasManyThroughMorph(
            $this,
            Treatment::class,
            'provider_treatment',
            'provider_type',
            'provider_id',
            'treatment_id'
        );
    }       

}
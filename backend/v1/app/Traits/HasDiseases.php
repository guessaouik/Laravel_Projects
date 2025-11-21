<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Disease;
use Relationship\Morph;

trait HasDiseases{
    
    public function diseases(){
        return Morph::hasManyThroughMorph(
            $this,
            Disease::class,
            'disease_provider',
            'provider_type',
            'provider_id',
            'disease_id'
        );
    }       

}
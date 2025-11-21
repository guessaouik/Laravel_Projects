<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Service;
use Relationship\Morph;

trait HasServices{

    public function services(){
        return Morph::hasManyThroughMorph(
            $this,
            Service::class,
            'provider_service',
            'provider_type',
            'provider_id',
            'service_id'
        );
    }
}


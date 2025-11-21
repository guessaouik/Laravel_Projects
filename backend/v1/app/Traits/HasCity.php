<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\City;
use Relationship\Morph;

trait HasCity{

    public function city(){
        return Morph::hasOneThroughMorph(
            $this,
            City::class,
            'city_provider',
            'provider_type',
            'provider_id',
            'city_id'
        );
    }
}
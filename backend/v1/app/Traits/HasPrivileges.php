<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Privilege;
use Relationship\Morph;

trait HasPrivileges{
    
    public function privileges(){
        return Morph::hasManyThroughMorph(
            $this,
            Privilege::class,
            'privilege_profile',
            'profile_type',
            'profile_id',
            'privilege_id'
        );
    }       

}
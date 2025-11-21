<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\User;
use Relationship\Morph;

trait HasUser{

    public function user(){
        return Morph::hasOneThroughMorph(
            $this,
            User::class,
            "profile_user",
            "profile_type",
            "profile_id",
            "user_id"
        );
    }
}
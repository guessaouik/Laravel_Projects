<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Post;
use Relationship\Morph;

trait HasPosts{

    public function posts(){
        return Morph::hasManyThroughMorph(
            $this,
            Post::class,
            "post_profile",
            "profile_type",
            "profile_id",
            "post_id"
        );
    }
}
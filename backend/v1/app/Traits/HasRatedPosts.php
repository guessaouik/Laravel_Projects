<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Post;
use Relationship\Morph;

trait HasRatedPosts{

    public function ratedPosts(){
        return Morph::hasManyThroughMorph(
            $this,
            Post::class,
            "post_ratings",
            "profile_type",
            "profile_id",
            "post_id"
        );
    }
}
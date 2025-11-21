<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Post;
use Relationship\Morph;

trait HasSavedPosts{

    public function savedPosts(){
        return Morph::hasManyThroughMorph(
            $this,
            Post::class,
            "saved_posts",
            "saver_type",
            "saver_id",
            "post_id"
        );
    }
}
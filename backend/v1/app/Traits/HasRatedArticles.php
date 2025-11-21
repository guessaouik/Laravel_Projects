<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Article;
use Relationship\Morph;

trait HasRatedArticles{

    public function ratedArticles(){
        return Morph::hasManyThroughMorph(
            $this,
            Article::class,
            "article_ratings",
            "profile_type",
            "profile_id",
            "article_id"
        );
    }
}
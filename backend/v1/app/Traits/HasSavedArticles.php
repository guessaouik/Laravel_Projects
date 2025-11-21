<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Article;
use Relationship\Morph;

trait HasSavedArticles{

    public function savedArticles(){
        return Morph::hasManyThroughMorph(
            $this,
            Article::class,
            "saved_articles",
            "saver_type",
            "saver_id",
            "article_id"
        );
    }
}
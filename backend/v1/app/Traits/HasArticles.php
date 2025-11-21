<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Article;
use Relationship\Morph;

trait HasArticles{

    public function articles(){
        return Morph::hasManyThroughMorph(
            $this,
            Article::class,
            'article_provider',
            'provider_type',
            'provider_id',
            'article_id'
        );
    }
}
<?php 


namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Review;
use Relationship\Morph;

trait HasRatedReviews{

    public function ratedReviews(){
        return Morph::hasManyThroughMorph(
            $this,
            Review::class,
            "review_ratings",
            "profile_type",
            "profile_id",
            "review_id"
        );
    }
}
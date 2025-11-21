<?php

namespace App\Traits;

include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Review;
use Relationship\Morph;

trait HasReviews{

    public function reviews(){
        return Morph::hasManyThroughMorph(
            $this,
            Review::class,
            "reviewed_reviewer",
            "reviewed_type",
            "reviewed_id",
            "review_id"
        );
    }
}


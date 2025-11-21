<?php

namespace App\Models;
use App\Traits\HasArticles;
use App\Traits\HasCity;
use App\Traits\HasCreatedReviews;
use App\Traits\HasPosts;
use App\Traits\HasRatedArticles;
use App\Traits\HasRatedPosts;
use App\Traits\HasRatedReviews;
use App\Traits\HasReviewed;
use App\Traits\HasReviewers;
use App\Traits\HasReviews;
use App\Traits\HasSavedArticles;
use App\Traits\HasSavedPosts;
use App\Traits\HasSavedProfiles;
use App\Traits\HasSpecialties;
use App\Traits\HasUser;
use App\Traits\IsProvider;

abstract class Provider extends MorphModel
{
    use HasArticles, HasCity, HasPosts, HasRatedArticles, HasRatedPosts,
    HasRatedReviews, HasReviewed, HasReviewers, HasSpecialties, HasUser,
    IsProvider, HasSavedPosts, HasSavedArticles, HasSavedProfiles, HasCreatedReviews,
    HasReviews;
    
    protected $hidden = ["password", ];

    protected array $collectionMorph_Methods= [
        "articles", "posts", "ratedArticles", "ratedPosts", "ratedReviews",
        "reviewed", "reviewers", "savedArticles", "savedPosts",
        "savedProfiles", "reviews", "createdReviews",
    ];

    protected array $modelMorph_Methods = [
        "city", "user"
    ];

    protected $fillable;
    public function __construct()
    {
        $this->fillable = [];
        $this->fillProviderFillable($this->fillable);
    }
}

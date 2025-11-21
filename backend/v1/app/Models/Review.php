<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Review extends MorphModel
{
    use HasFactory;
    // change created_at timestamp to "review_date";
    protected $primaryKey = "review_id";
    protected $fillable = [
        "rating", "content"
    ];  
    protected array $modelMorph_Methods = ["creator", "reviewed"];
    protected array $collectionMorph_Methods = ["raters"];

    public function creator(){
        return Morph::morphsOne($this, "reviewed_reviewer", "review_id", "reviewer_type", "reviewer_id");
    }

    public function reviewed(){
        return Morph::morphsOne($this, "reviewed_reviewer", "review_id", "reviewed_type", "reviewed_id");
    }

    public function raters(){
        return Morph::morphsMany($this, "review_ratings", "review_id", "profile_type", "profile_id");
    }
    
}

<?php

namespace App\Models;

use App\Traits\HasSpecialties;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Relationship\Morph;

class Article extends MorphModel
{
    use HasFactory, HasSpecialties;

    // change create_at timestamp to in return "publishing_date";
    protected $primaryKey = "article_id";
    protected $fillable = ["photo", "title", "content"];
    protected array $collectionMorph_Methods = ["raters", "specialties"];
    protected array $modelMorph_Methods = ["creator"];

    public function creator(){
        return Morph::morphsOne($this, 'article_provider', 'article_id', 'provider_type', 'provider_id');
    }

    // don't use unless necessary
    public function raters(){
        return Morph::morphsMany($this, 'article_ratings', 'article_id', 'profile_type', 'profile_id');
    }
}

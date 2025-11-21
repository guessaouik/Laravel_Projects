<?php

namespace App\Models;

use App\Traits\HasSpecialties;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Post extends MorphModel
{
    use HasFactory, HasSpecialties;
    protected $primaryKey = "post_id";
    protected $fillable = ["parent_id", "title", "content"];
    protected array $collectionMorph_Methods = ["specialties", "raters"];
    protected array $modelMorph_Methods = ["creator"];

    public function creator(){
        return Morph::morphsOne($this, 'post_profile', 'post_id', 'profile_type', 'profile_id');
    }
    
    public function raters(){
        return Morph::morphsMany($this, 'post_ratings', 'post_id', 'profile_type', 'profile_id');
    }
}

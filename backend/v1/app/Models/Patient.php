<?php

namespace App\Models;

use App\Traits\HasCreatedReviews;
use App\Traits\HasPosts;
use App\Traits\HasPrivileges;
use App\Traits\HasRatedArticles;
use App\Traits\HasRatedPosts;
use App\Traits\HasRatedReviews;
use App\Traits\HasReviewed;
use App\Traits\HasReviews;
use App\Traits\HasSavedArticles;
use App\Traits\HasSavedPosts;
use App\Traits\HasSavedProfiles;
use App\Traits\HasUser;
use App\Traits\IsPerson;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends MorphModel
{
    use HasFactory, HasPosts, HasPrivileges, HasRatedArticles, HasRatedPosts, HasRatedReviews,
    HasReviewed, HasUser, IsPerson, HasSavedPosts, HasSavedArticles, HasSavedProfiles, HasCreatedReviews,
    HasReviews;
    
    protected $primaryKey = "patient_id";
    protected $fillable = ["birth_date", "gender", "blood_type"];
    protected array $collectionMorph_Methods = [
        "posts", "privileges", "ratedArticles", "ratedPosts", "ratedReviews", "reviewed",
        "savedPosts", "savedArticles", "savedProfiles", "createdReviews",
    ];
    protected array $modelMorph_Methods = ["user"];

    public function __construct()
    {
        $this->fillPersonFillable($this->fillable);
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, "patient_id", "patient_id");
    }

}

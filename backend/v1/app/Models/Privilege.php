<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Privilege extends MorphModel
{
    use HasFactory;
    protected $primaryKey = "privilege_id";
    protected $fillable = ["name"];
    protected array $collectionMorph_Methods = ["profiles"];

    public function profiles(){
        return Morph::morphsMany($this, 'privilege_profile', 'privilege_id', 'profile_type', 'profile_id');
    }
    
}

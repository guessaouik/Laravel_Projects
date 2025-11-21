<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Specialty extends MorphModel
{
    use HasFactory;
    protected $primaryKey = "specialty_id";
    protected $fillable = ["name"];
    protected array $collectionMorph_Methods = ["components"];

    public function components(){
        return Morph::morphsMany($this, 'component_specialty', 'specialty_id', 'component_type', 'component_id');
    }
}

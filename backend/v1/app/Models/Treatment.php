<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Treatment extends MorphModel
{
    use HasFactory;
    protected $primaryKey = 'treatment_id';
    protected $fillable = ['name'];
    protected array $collectionMorph_Methods = ["providers"];

    public function providers(){
        return Morph::morphsMany($this, 'provider_treatment', 'treatment_id', 'provider_type', 'provider_id');
    }
}

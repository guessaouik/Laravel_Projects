<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Disease extends MorphModel
{
    use HasFactory;
    protected $primaryKey = 'disease_id';
    protected $fillable = ['name'];
    protected array $collectionMorph_Methods = ["providers"];

    public function providers(){
        return Morph::morphsMany($this, 'disease_provider', 'disease_id', 'provider_type', 'provider_id');
    }

}

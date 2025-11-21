<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class City extends MorphModel
{
    use HasFactory;

    protected $primaryKey = "city_id";
    protected $fillable = ["state_id", "name"];
    protected array $collectionMorph_Methods = ["providers"];

    public function state(){
        return $this->belongsTo(State::class, "state_id", "state_id");
    }

    public function providers(){
        return Morph::morphsMany($this, 'city_provider', 'city_id', 'provider_type', 'provider_id');
    }
}

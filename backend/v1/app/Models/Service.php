<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Service extends MorphModel
{
    use HasFactory;
    protected $primaryKey = "service_id";
    protected $fillable = ["name"];
    protected array $collectionMorph_Methods = ["providers"];

    public function providers(){
        return Morph::morphsMany($this, 'provider_service', 'service_id', 'provider_type', 'provider_id');
    }

}

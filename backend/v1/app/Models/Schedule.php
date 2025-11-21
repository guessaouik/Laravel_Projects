<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Schedule extends MorphModel
{
    use HasFactory;

    protected $primaryKey = "schedule_id";
    protected $fillable = ["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"];
    protected array $modelMorph_Methods = ["provider"];

    public function provider(){
        return Morph::morphsOne($this, "provider_schedule", "schedule_id", "provider_type", "provider_id");
    }
}

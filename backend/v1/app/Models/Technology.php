<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;
    protected $primaryKey = "technology_id";
    protected $fillable = ["name"];

    public function mics(){
        return $this->belongsToMany(MIC::class, "mic_technology", "technology_id", "mic_id");
    }
}

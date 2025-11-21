<?php

namespace App\Models;

use App\Traits\HasSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MIC extends Institution
{
    use HasFactory, HasSchedule;
    protected $primaryKey = "mic_id";

    public function __construct()
    {
        $this->modelMorph_Methods = array_merge($this->modelMorph_Methods, ["schedule"]);
        parent::__construct();
    }

    public function technologies(){
        return $this->belongsToMany(Technology::class, "mic_technology", "mic_id", "technology_id");
    }
}

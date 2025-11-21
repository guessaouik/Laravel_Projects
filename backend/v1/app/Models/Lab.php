<?php

namespace App\Models;

use App\Traits\HasSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Institution
{
    use HasFactory, HasSchedule;
    protected $primaryKey = "lab_id";

    public function __construct()
    {
        $this->modelMorph_Methods = array_merge($this->modelMorph_Methods, ["schedule"]);
        parent::__construct();
    }

    public function tests(){
        return $this->belongsToMany(Test::class, "lab_test", "lab_id", "test_id");
    }   

}

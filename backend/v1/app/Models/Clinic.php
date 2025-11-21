<?php

namespace App\Models;

use App\Traits\HasDiseases;
use App\Traits\HasDoctors;
use App\Traits\HasSchedule;
use App\Traits\HasServices;
use App\Traits\HasSpecialties;
use App\Traits\HasTreatments;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Institution
{
    use HasFactory, HasDoctors, HasServices, HasTreatments, HasDiseases, HasSchedule, HasSpecialties;

    protected $primaryKey = "clinic_id";

    public function __construct(){
        $this->collectionMorph_Methods = array_merge($this->collectionMorph_Methods, ["doctors", "services", "treatments", "diseases", "specialties"]);
        $this->modelMorph_Methods = array_merge($this->modelMorph_Methods, ["schedule"]);
        parent::__construct();
    }

}

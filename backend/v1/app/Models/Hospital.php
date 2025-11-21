<?php

namespace App\Models;

use App\Traits\HasDiseases;
use App\Traits\HasDoctors;
use App\Traits\HasServices;
use App\Traits\HasSpecialties;
use App\Traits\HasTreatments;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Institution
{
    use HasFactory, HasDoctors, HasServices, HasTreatments, HasDiseases, HasSpecialties;
    protected $primaryKey = "hospital_id";
    public function __construct(){
        $this->collectionMorph_Methods = array_merge($this->collectionMorph_Methods, ["doctors", "services", "treatments", "diseases", "specialties"]);
        parent::__construct();
    }
    
}

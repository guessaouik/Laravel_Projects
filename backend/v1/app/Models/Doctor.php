<?php

namespace App\Models;

use App\Traits\HasDiseases;
use App\Traits\HasSchedule;
use App\Traits\HasSpecialties;
use App\Traits\HasTreatments;
use App\Traits\IsPerson;
use Database\Seeders\AppointmentSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Relationship\Morph;

class Doctor extends Provider
{
    use HasFactory, HasTreatments, HasDiseases, HasSpecialties, IsPerson, HasSchedule;

    protected $primaryKey = "doctor_id";

    public function __construct()
    {
        $this->collectionMorph_Methods = array_merge($this->collectionMorph_Methods, ["treatments", "diseases", "specialties", "institutions"]);
        $this->modelMorph_Methods = array_merge($this->modelMorph_Methods, ["schedule"]);
        parent::__construct();
        $this->fillPersonFillable($this->fillable);
    }

    public function appointmentSchedules(){
        return $this->hasMany(AppointmentSchedule::class, "doctor_id", "doctor_id");
    }
    
    public function institutions(){
        return Morph::morphsMany($this, 'doctor_institution', 'doctor_id', 'institution_type', 'institution_id');
    }
}

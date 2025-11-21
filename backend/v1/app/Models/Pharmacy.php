<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pharmacy extends Institution 
{
    use HasFactory;
    protected $primaryKey = "pharmacy_id";

    public function equipments(){
        return $this->belongsToMany(MedicalEquipment::class, "equipment_pharmacy", "pharmacy_id", "equipment_id");
    }
}

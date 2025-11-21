<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalEquipment extends Model
{
    use HasFactory;
    protected $primaryKey = "equipment_id";
    protected $fillable = ["name"];
    protected $table = "medical_equipments";

    public function pharmacies(){
        return $this->belongsToMany(Pharmacy::class, "equipment_pharmacy", "equipment_id", "pharmacy_id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    // attributes
    protected $primaryKey = "appointment_id";
    protected $fillable = ["schedule_id", "patient_id", "info", "time", 'status', "session_number"];

    public function schedule(){
        return $this->belongsTo(AppointmentSchedule::class, "schedule_id", "schedule_id");
    }

    public function patient(){
        return $this->belongsTo(Doctor::class, "patient_id", "patient_id"); 
    }
}

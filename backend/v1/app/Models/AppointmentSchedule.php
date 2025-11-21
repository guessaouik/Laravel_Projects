<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AppointmentSchedule extends Model
{
    use HasFactory, Prunable;
    protected $primaryKey = "schedule_id";
    protected $fillable = ["doctor", "date", "interval", "appointments_number"];

    public function doctor(){
        return $this->belongsTo(Doctor::class, "doctor_id", "doctor_id");
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, "schedule_id", "schedule_id"); 
    }

    public function prunable() : Builder 
    {
        return static::where("date", "<=", now()->subDay()->format("Y-m-d"));     
    }
}

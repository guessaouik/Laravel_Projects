<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    use HasFactory;
    protected $fillable = ["profile_type", "profile_id", "specialty_id"];
    private const TABLE_ALIASES = [
        "Hospital" => "h",
        "Doctor" => "d",
        "MIC" => "m",
        "Pharmacy" => "ph",
        "Lab" => "l",
        "Clinic" => "c",
        "Patient" => "p"
    ];

    public function profile(){
        return call_user_func_array(["App\\Models\\".array_search($this->profile_type, static::TABLE_ALIASES), "find"], [$this->profile_id]);
    }

    public function specialty(){
        return $this->belongsTo(Specialty::class, "specialty_id", "specialty_id");
    }
}

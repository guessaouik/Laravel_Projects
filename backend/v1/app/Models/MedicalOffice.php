<?php

namespace App\Models;

use App\Traits\HasCity;
use App\Traits\HasDoctors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalOffice extends Model
{
    use HasFactory, HasCity, HasDoctors;
    protected $primaryKey = "office_id";
    protected $fillable = ["name", "longitude", "latitude", "address"];
    protected array $modelMorph_Methods = ["city"];
    protected array $collectionMorph_Methods = ["doctors"];
}

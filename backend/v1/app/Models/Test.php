<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $primaryKey = "test_id";
    protected $fillable = ["name"];

    public function labs(){
        return $this->belongsToMany(Lab::class, "lab_test", "test_id", "lab_id");
    }
}

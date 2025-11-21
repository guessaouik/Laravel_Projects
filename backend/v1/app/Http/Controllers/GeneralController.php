<?php

namespace App\Http\Controllers;

use App\Http\Resources\General\ScheduleResource;
use App\Http\Resources\General\SpecialtyResource;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Rules\ValidateTypePermission;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getSpecialties(Request $request){
        $pattern = $request->pattern ?? "";
        if ($request->paginate !== null && $request->paginate === true){
            return SpecialtyResource::collection(
                Specialty::where("name", "like", "%$pattern%")
                ->paginate($request->perPage ?? 15)
            );
        } 
        return SpecialtyResource::collection(Specialty::where("name", "like", "%$pattern%")->get());
    }


}

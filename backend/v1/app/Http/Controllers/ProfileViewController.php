<?php

namespace App\Http\Controllers;

use App\Http\Resources\General\ScheduleResource;
use App\Http\Resources\Profile\View\ClinicResource;
use App\Http\Resources\Profile\View\DoctorResource;
use App\Http\Resources\Profile\View\HospitalResource;
use App\Http\Resources\Profile\View\LabResource;
use App\Http\Resources\Profile\View\MICResource;
use App\Http\Resources\Profile\View\PatientResource;
use App\Http\Resources\Profile\View\PharmacyResource;
use App\Rules\ProfileType;
use App\Rules\ValidateTypePermission;
use Error;
use Illuminate\Http\Request;

class ProfileViewController extends Controller
{
    public const TYPE_RESOURCE = [
        "doctor" => DoctorResource::class,
        "patient" => PatientResource::class,
        "hospital" => HospitalResource::class,
        "clinic" => ClinicResource::class,
        "pharmacy" => PharmacyResource::class,
        "lab" => LabResource::class,
        "mic" => MICResource::class,
    ];

    public function get(Request $request){
        $request->validate([
            "type" => ["required", new ProfileType],
            "id" => "required",
        ]);

        $resource = static::TYPE_RESOURCE[$request->type];
        $class = TYPE_MODEL[$request->type];
        return (new $resource(call_user_func_array([$class, "find"], [$request->id])))->toArray($request);
    } 

    public function getSchedule(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor", "hospital", "clinic", "mic", "lab", "pharmacy")],
            "id" => "required",
        ]);

        $model = call_user_func_array([TYPE_MODEL[$request->type], "find"], [$request->id]);
        return new ScheduleResource($model);
    }
}

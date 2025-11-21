<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "patientId" => $this->patientId,
            "firstName" => $this->firstname,
            "lastName" => $this->lastname,
            "photo" => $this->photo,
            "gender" => $this->gender,
            "bloodType" => $this->blood_type
        ];
    }
}

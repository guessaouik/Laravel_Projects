<?php

namespace App\Http\Resources\Appointment;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "doctorId" => $this->doctor_id,
            "firstName" => $this->firstname,
            "lastName" => $this->lastname,
            "photo" => $this->photo,
            "available" => (new Profile($this->resource))->isAvailable(date("d-m-Y H:i"))
        ];
    }
}

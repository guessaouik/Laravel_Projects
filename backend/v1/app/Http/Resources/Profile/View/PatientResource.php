<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
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
        $profile = new Profile($this->resource);
        return [
            "id" => $this->resource->getKey(),
            "type" => "patient",
            "firstName" => $this->firstname,
            "lastName" => $this->lastname,
            "address" => $this->address,
            "profile" => $profile->getProfilePhoto(),
            "socials" => $profile->getSocialsArray(),
            "birthDate" => $this->birth_date,
            "gender" => $this->gender,
            "bloodType" => $this->blood_type
        ];
    }
}

<?php

namespace App\Http\Resources\Home;

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
        $profile = new Profile($this->resource);
        return [
            "type" => "doctor",
            "id" => $this->resource->getKey(),
            "photo" => $profile->getPhotosArray(),
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "rating" => $this->rating,
            "specialties" => $profile->getFilterValues("specialties"),
            "address" => $this->address,
            "views" => $this->views,
            "about" => $this->about,
            "available" => $profile->isAvailable(),
        ];
    }
}

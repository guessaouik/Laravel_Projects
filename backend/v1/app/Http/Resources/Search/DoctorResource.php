<?php

namespace App\Http\Resources\Search;

use App\Http\Controllers\Helpers\Profile;
use App\Http\Resources\General\ScheduleResource;
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
            "name" => $this->firstname . " " . $this->lastname,
            "about" => $this->about,
            "photo" => $profile->getPhotosArray(),
            "address" => $this->address,
            "rating" => $this->rating,
            "views" => $this->views,
            "available" => $profile->isAvailable(date("d-m-Y H:i")),
            "specialties" => $this->specialties->pluck("name")->toArray(),
        ];
    }
}

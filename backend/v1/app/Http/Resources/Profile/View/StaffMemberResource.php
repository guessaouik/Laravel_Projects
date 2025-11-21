<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
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
            "doctorId" => $this->resource->getKey(),
            "name" => $this->firstname . " " . $this->lastname,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "address" => $this->address,
            "photo" => $profile->getPhotosArray(),
            "about" => $this->about,
            "views" => $this->views,
            "ratings" => $this->ratings,
            "available" => $profile->isAvailable()
        ];
    }
}

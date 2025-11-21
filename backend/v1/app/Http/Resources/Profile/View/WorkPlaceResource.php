<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkPlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \App\Models\MedicalOffice){
            return [
                "name" => $this->name,
                "longitude" => $this->longitude,
                "latitude" => $this->latitude,
                "address" => $this->address
            ];
        }
        $profile = new Profile($this->resource);
        return [
            "id" => $this->resource->getKey(),
            "type" => array_search($this->resource::class, TYPE_MODEL),
            "name" => $this->name,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "address" => $this->address,
            "profile" => $profile->getProfilePhoto(),
            "about" => $this->about,
            "views" => $this->views,
            "rating" => $this->rating,
            "available" => $profile->isAvailable(),
        ];
    }
}

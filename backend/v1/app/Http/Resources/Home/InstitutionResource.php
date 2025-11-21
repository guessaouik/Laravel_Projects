<?php

namespace App\Http\Resources\Home;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
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
            "type" => array_search($this->resource::class, TYPE_MODEL),
            "id" => $this->resource->getKey(),
            "name" => $this->name,
            "photo" => $profile->getPhotosArray(),
            "about" => $this->about,
            "address" => $this->address,
            "rating" => $this->rating,
            "views" => $this->views,
            "available" => $profile->isAvailable(),
        ];
    }
}

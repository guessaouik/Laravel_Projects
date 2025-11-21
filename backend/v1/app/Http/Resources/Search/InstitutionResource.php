<?php

namespace App\Http\Resources\Search;

use App\Http\Controllers\Helpers\Profile;
use Error;
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
        $type = array_search($this->resource::class, TYPE_MODEL);
        $profile = new Profile($this->resource);
        $result = [
            "type" => $type,
            "id" => $this->resource->getKey(),
            "name" => $this->name,
            "about" => $this->about,
            "photos" => $profile->getPhotosArray(), // change to $this->photo if not multiple photos
            "address" => $this->address,
            "rating" => $this->rating,
            "views" => $this->views,
            "available" => $profile->isAvailable(date("d-m-Y H:i")),
        ];

        if ($type === "clinic" || $type === "hospital"){
            $result["specialties"] = $this->specialties->pluck("name")->toArray();
        }
        return $result;
    }
}

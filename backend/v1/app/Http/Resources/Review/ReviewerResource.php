<?php

namespace App\Http\Resources\Review;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => array_search($this->resource::class, TYPE_MODEL),
            "id" => $this->resource->getKey(),
            "name" => $this->name ?? $this->firstname . " " . $this->lastname,
            "photo" => (new Profile($this->resource))->getProfilePhoto(),
            "about" => $this->about,
            "views" => $this->views,
            "rating" => $this->rating,
        ];
    }
}

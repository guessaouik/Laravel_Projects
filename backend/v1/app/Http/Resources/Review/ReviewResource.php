<?php

namespace App\Http\Resources\Review;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "reviewId" => $this->resource->getKey(),
            "date" => (new DateTime($this->updated_at))->format("d-m-Y H:i"),
            "content" => $this->content,
            "rating" => $this->rating,
            "likes" => $this->likes,
            "creator" => new ReviewerResource($this->resource->creator),
            "value" => $this->value,
        ];
    }
}

<?php

namespace App\Http\Resources\Profile\SavedItems;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "postId" => $this->resource->getKey(),
            "title" => $this->title,
            "content" => $this->content,
            "photo" => $this->photo,
            "likes" => $this->likes,
            "replies" => $this->replies,
        ];
    }
}

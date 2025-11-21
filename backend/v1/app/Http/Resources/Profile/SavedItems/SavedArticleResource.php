<?php

namespace App\Http\Resources\Profile\SavedItems;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "articleId" => $this->resource->getKey(),
            "photo" => $this->photo,
            "title" => $this->title,
            "content" => $this->content,
            "likes" => $this->likes,
            "publishedAt" => $this->updated_at,
        ];
    }
}

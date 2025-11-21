<?php

namespace App\Http\Resources\Home;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "articleId" => $this->getKey(),
            "title" => $this->title,
            "content" => $this->content,
            "photo" => $this->photo,
            "likes" => $this->likes,
            "publishedAt" => $this->created_at,
            "specialties" => (new Profile($this->resource))->getFilterValues("specialties"),
        ];
    }
}

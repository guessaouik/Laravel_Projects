<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCreatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->getKey(),
            "name" => $this->name ?? ($this->firstname . " " . $this->lastname),
            "about" => $this->about,
            "views" => $this->views,
            "rating" => $this->rating
        ];
    }
}

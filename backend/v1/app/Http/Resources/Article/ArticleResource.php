<?php

namespace App\Http\Resources\Article;

use App\Http\Controllers\Helpers\Photo;
use App\Http\Resources\General\SpecialtyResource;
use App\Models\Hospital;
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
        $returnArray = [
            "articleId" => $this->article_id,
            "photo" => Photo::getPhotoAbsolutePath($this->photo),
            "title" => $this->title,
            "content" => $this->content,
            "upVotes" => $this->upvotes,
            "downVotes" => $this->downvotes,
            "publishedAt" => $this->created_at,
            "lastModified" => $this->updated_at,
            "specialties" => SpecialtyResource::collection($this->specialties),
            "creator" => new ArticleCreatorResource($this->creator),
        ];
        if ($this->ratingValue !== null){
            $returnArray["value"] = $this->ratingValue;
        }
        return $returnArray;
    }
}

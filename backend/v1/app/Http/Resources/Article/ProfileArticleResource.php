<?php

namespace App\Http\Resources\Article;

use App\Http\Controllers\Helpers\Photo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * a resource for articles displayed in a profile, whether that be a creator or rater's profile.
 */
class ProfileArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "articleId" => $this->article_id,
            "photo" => Photo::getPhotoAbsolutePath($this->photo),
            "title" => $this->title,
            "content" => $this->content,
            "upVotes" => $this->upvotes,
            "downVotes" => $this->downvotes,
            "publishedAt" => $this->created_at,
            "liked" => $this->rating,
            "lastModified" => $this->updated_at,
        ];
    }
}

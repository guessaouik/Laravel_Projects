<?php

namespace App\Http\Resources\Article;

use App\Http\Controllers\Helpers\Photo;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RaterArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $article = Article::find($this->articleId);
        return [
            "articleId" => $article->article_id,
            "photo" => Photo::getPhotoAbsolutePath($this->photo),
            "title" => $article->title,
            "content" => $article->content,
            "upVotes" => $article->upvotes,
            "downVotes" => $article->downvotes,
            "publishedAt" => $article->created_at,
            "lastModified" => $article->updated_at,
            "rating" => $this->value
        ];
    }
}

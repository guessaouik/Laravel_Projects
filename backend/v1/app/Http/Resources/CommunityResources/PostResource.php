<?php

namespace App\Http\Resources\CommunityResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->post_id, 
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'content' => $this->content,
            'likes' => $this->likes,
            'replies' => $this->replies,
            'published at' => $this->created_at
        ];    
    }
}

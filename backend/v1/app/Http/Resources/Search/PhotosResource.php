<?php

namespace App\Http\Resources\Search;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $photos = explode(";", $this->resource);
        $result = [
            "other" => []
        ];
        foreach ($photos as $photo){
            if (str_contains($photo, "profile")){
                $result["profile"] = explode(":", $photo)[1];
                continue;
            }
            $result["other"][] = $photo;
        }
        return $result;
    }
}

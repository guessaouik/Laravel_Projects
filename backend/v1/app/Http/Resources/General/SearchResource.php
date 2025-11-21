<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public static string $resourceClass;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $modelResource = new static::$resourceClass($this->resource);

        return array_merge($modelResource->toArray($request), [
            "matches" => $this->matches,
            "indexes" => $this->indexes
        ]);
    }
}

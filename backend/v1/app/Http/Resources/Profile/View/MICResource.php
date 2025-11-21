<?php

namespace App\Http\Resources\Profile\View;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MICResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {        
        $result = array_merge(
            (new InstitutionResource($this->resource))->toArray($request),
            [
                "privileges" => $this->privileges->pluck("name")->toArray(),
                "technologies" => $this->technologies->pluck("name")->toArray(),
            ]
        );

        ProviderResource::addValueColumns(["technologies"], $this->resource, $result);
        return $result;
    }
}

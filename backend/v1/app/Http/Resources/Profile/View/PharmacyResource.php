<?php

namespace App\Http\Resources\Profile\View;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PharmacyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result =  (new InstitutionResource($this->resource))->toArray($request);
        ProviderResource::addValueColumns(["equipments", "diseases", "treatments", "privileges"], $this->resource, $result);
        return $result;
    }
}

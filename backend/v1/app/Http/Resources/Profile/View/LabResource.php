<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result =  array_merge(
            (new InstitutionResource($this->resource))->toArray($request),
            [
                "privileges" => $this->privileges->pluck("name")->toArray(),
                "tests" => $this->tests->pluck("name")->toArray(),
            ]
        );

        ProviderResource::addValueColumns(["tests"], $this->resource, $result);
        return $result;
    }
}

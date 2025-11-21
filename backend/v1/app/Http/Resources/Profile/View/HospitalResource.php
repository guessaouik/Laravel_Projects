<?php

namespace App\Http\Resources\Profile\View;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
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
                "staff" => StaffMemberResource::collection($this->resource->doctors),
            ]
        );
        ProviderResource::addValueColumns(["treatments", "specialties", "diseases", "privileges", "services"], $this->resource, $result);
        return $result;
    }
}

<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ClinicResource extends JsonResource
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

        ProviderResource::addValueColumns(["services", "treatments", "diseases", "privileges", "specialties"], $this->resource, $result);
        return $result;
    }
}

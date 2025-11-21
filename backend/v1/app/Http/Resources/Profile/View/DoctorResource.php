<?php

namespace App\Http\Resources\Profile\View;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = new Profile($this->resource);
        $result = array_merge(
            (new ProviderResource($this->resource))->toArray($request),
            [
                "photo" => $profile->getPhotosArray(),
                "schedule" => $profile->getDisplaySchedule($request->offset ?? SCHEDULE_DAYS_DEFAULT_OFFSET, $request->number ?? SCHEDULE_DAYS_DEFAULT_NUMBER, $request->withAppointments ?? true),
                "institutions" => WorkPlaceResource::collection($this->institutions),
            ]
        );

        unset($result["name"]);
        $result["firstName"] = $this->firstname;
        $result["lastName"] = $this->lastname;
        ProviderResource::addValueColumns(["treatments", "specialties", "diseases"], $this->resource, $result);
        return $result;
    }
}

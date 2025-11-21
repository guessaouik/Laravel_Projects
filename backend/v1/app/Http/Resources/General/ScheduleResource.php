<?php

namespace App\Http\Resources\General;

use App\Http\Controllers\Helpers\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = new Profile($this->resource);
        return $profile->getDisplaySchedule($request->offset ?? 0, $request->number ?? 5, $request->withAppointments ?? true) ?? [];
    }
}

<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "scheduleId" => $this->schedule_id,
            "date" => $this->date,
            "time" => explode("-", $this->interval),
            "sessionsNumber" => $this->appointments_number,
        ];
    }
}

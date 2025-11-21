<?php

namespace App\Http\Resources\Appointment;

use App\Http\Controllers\AppointmentController;
use App\Models\Patient;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status === null ? "no response" : ($this->status ? "accepted" : "canceled");
        $schedule = $this->resource->schedule;
        return [
            "appointmentId" => $this->appointment_id,
            "patient" => new PatientResource($this->resource->patient),
            "info" => $this->info,
            "date" => $schedule->date,
            "time" => AppointmentController::getInterval($this->sessionNumber, $schedule->interval, $schedule->appointments_number),
            "status" => $status
        ];
    }
}

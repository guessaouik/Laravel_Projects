<?php

namespace App\Http\Resources\Appointment;

use App\Http\Controllers\AppointmentController;
use App\Models\Doctor;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientAppointmentResource extends JsonResource
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
            "doctor" => new DoctorResource($schedule->doctor),
            "info" => $this->info,
            "time" => AppointmentController::getInterval($this->sessionNumber, $schedule->interval, $schedule->appointments_number),
            "date" => $schedule->date,
            "status" => $status,
        ];
    }
}

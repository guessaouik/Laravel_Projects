<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Paginate;
use App\Http\Resources\Appointment\AppointmentScheduleResource;
use App\Http\Resources\Appointment\DoctorAppointmentResource;
use App\Http\Resources\Appointment\PatientAppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use App\Models\Doctor;
use App\Models\Patient;
use App\Rules\ValidateTypePermission;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{

    private static function convertToMinutes(int $durationTimeStamp) : int{
        return $durationTimeStamp / 60;
    }

    public static function getInterval(int $sessionNumber, string $appointmentsInterval, int $totalSessions) : array{
        if ($sessionNumber > $totalSessions){
            return null;
        }
        [$startTime, $endTime] = array_map(fn($time) => strtotime($time), explode("-", $appointmentsInterval));
        $timeDifference = abs($endTime - $startTime);
        $minutesPerSession = DateInterval::createFromDateString(static::convertToMinutes($timeDifference / $totalSessions) . " minutes");
        $timeToDesiredSession = DateInterval::createFromDateString(static::convertToMinutes(($timeDifference / $totalSessions) * ($sessionNumber - 1)) . " minutes");
        $sessionStartTime = (new DateTime(date("H:i", $startTime)))->add($timeToDesiredSession);
        $sessionEndTime = (new DateTime(date("H:i", $startTime)))->add($timeToDesiredSession);
        $sessionEndTime->add($minutesPerSession);
        return [$sessionStartTime->format("H:i"), $sessionEndTime->format("H:i")];
    }
    
    private function getPatientAppointments(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "patientId" => "required"
        ]);
        
        static::deleteCanceled($request->patientId);
        static::setCanceledViewDates($request->patientId);

        // getting appointments
        $date = $request->date === null ? "" : (new DateTime($request->date))->format("Y-m-d");
        $appointments = Patient::find($request->patientId)->appointments;
        if ($date !== ""){
            $appointments = $appointments->filter(fn($appointment) => $appointment->schedule->date === $date)->values();
        }
        return PatientAppointmentResource::collection(Paginate::paginate($appointments, $request->perPage ?? DEFAULT_PER_PAGE));
    }

    private function getDoctorAppointments(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor")],
            "doctorId" => "required"
        ]);

        $doctor = Doctor::find($request->doctorId);
        $scheduleIds = $doctor->appointmentSchedules->pluck(["schedule_id"])->toArray();

        $appointments = Appointment::whereIn("schedule_id", $scheduleIds);
        $date = $request->date === null ? "" : (new DateTime($request->date))->format("Y-m-d");
        $appointments = Patient::find($request->patientId)->appointments;
        if ($date !== ""){
            $appointments = $appointments->filter(fn($appointment) => $appointment->schedule->date === $date)->values();
        }

        return DoctorAppointmentResource::collection(Paginate::paginate($appointments, $request->perPage ?? DEFAULT_PER_PAGE));
    }

    private function getAppointment(Request $request){
        $request->validate([
            "appointmentId" => "required"
        ]);

        $appointment = Appointment::find($request->appointmentId);
        return array_merge(
            (new DoctorAppointmentResource($appointment))->toArray($request),
            (new PatientAppointmentResource($appointment))->toArray($request)
        );
    }

    public function get(Request $request){
        if ($request->patientId !== null){
            return $this->getPatientAppointments($request);
        }
        if ($request->doctorId !== null){
            return $this->getDoctorAppointments($request);
        }
        if ($request->appointmentId !== null){
            return $this->getAppointment($request);
        }
    }

    public function updateAppointmentInfo(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "appointmentId" => "required",
            "scheduleId" => "required",
            "sessionNumber" => "required",
        ]);

        $appointment = Appointment::find($request->appointmentId);
        $appointment->schedule_id = $request->scheduleId;
        $appointment->session_number = $request->sessionNumber;
        $appointment->save();
        return new PatientAppointmentResource($appointment);
    }

    public function delete(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "appointmentId" => "required"
        ]);

        Appointment::find($request->appointmentId)->delete();
        return ["message" => "deleted successfully"];
    }

    public function book(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "patientId" => "required",
            "doctorId" => "required",
            "scheduleId" => "required",
            "sessionNumber" => "required",
        ]);

        $schedule = AppointmentSchedule::find($request->scheduleId);

        $appointment = Appointment::create([
            "patient_id" => $request->patientId,
            "session_number" => $request->sessionNumber,
            "info" => $request->info ?? "",
            "schedule_id" => $schedule->getKey(),
        ]);

        return array_merge(
            (new DoctorAppointmentResource($appointment))->toArray($request),
            (new PatientAppointmentResource($appointment))->toArray($request)
        );
    }

    public function getSessions(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "scheduleId" => "required"
        ]);

        $schedule = AppointmentSchedule::find($request->scheduleId);
        
        $bookedSessions = $schedule->appointments->pluck("session_number")->toArray();
        $unbookedSessions = [];
        for ($i = 0; $i < $schedule->appointments_number; $i++){
            if (in_array($i, $bookedSessions)){
                continue;
            }
            $unbookedSessions[] = $i;
        }
        $availableIntervals = [];
        foreach ($unbookedSessions as $number){
            $availableIntervals[] = ["number" => $number, "interval" => static::getInterval($number, $schedule->interval, $schedule->appointments_number)];
        } 
        return $availableIntervals;
    }

    public function getDates(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("patient")],
            "doctorId" => "required",
        ]);
        if ($request->date !== null){
            $schedules = AppointmentSchedule::where("doctor_id", "=", $request->doctorId)->where("date", "=", $request->date)->get();
        } else {
            $doctor = Doctor::find($request->doctorId);
            $schedules = $doctor->appointmentSchedules;
        }

        $doctorDates = [];
        foreach ($schedules as $schedule){
            $doctorDates[] = [
                "date" => (new DateTime($schedule->date))->format("d-m-Y"), 
                "time" => $schedule->interval,
                "scheduleId" => $schedule->schedule_id
            ];
        }

        return $doctorDates;
    }

    public function createBookingsInfo(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor")],
            "doctorId" => "required",
            // d-m-Y
            "date" => "required",
            // array[2]
            "time" => "required",
            // int
            "sessionsNumber" => "required"
        ]);

        $schedule = AppointmentSchedule::create([
            "doctor_id" => $request->doctorId,
            "date" => (new DateTime($request->date))->format("Y-m-d"),
            "interval" => $request->time,
            "appointments_number" => $request->sessionNumber,
        ]);

        return new AppointmentScheduleResource($schedule);
    }

    public function updateBookingsInfo(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor")],
            "scheduleId" => "required",
            // d-m-Y
            "date" => "required",
            // array[2]
            "time" => "required",
            // int
            "sessionsNumber" => "required"
        ]);

        $schedule = AppointmentSchedule::find($request->scheduleId);
        $schedule->date = (new DateTime($request->date))->format("Y-m-d");
        $schedule->interval = implode("-", $request->time);
        $schedule->appointments_number = $request->sessionsNumber;

        $schedule->save();
        return new AppointmentScheduleResource($schedule);        
    }

    public function deleteBookingsInfo(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor")],
            "scheduleId" => "required"
        ]);

        AppointmentSchedule::find($request->scheduleId)->delete();

        return ["message" => "deleted successfully"];
    }

    public function set(Request $request){
        $request->validate([
            "type" => ["required", new ValidateTypePermission("doctor")],
            "appointmentIds" => "required",
            "value" => "required"
        ]);

        $appointmentIds = is_array($request->appointmentIds) ? $request->appointmentIds : [$request->appointmentIds];
        foreach ($appointmentIds as $id){
            $appointment = Appointment::find($id);
            $appointment->status = $request->value;
            if ($request->value === false) {
                $appointment->canceled_status_view_date = now()->format("Y-m-d H:i");
            };
            $appointment->save();
        }
    }

    public function update(Request $request){
        if ($request->type === "doctor"){
            return $this->set($request);
        } 
        if ($request->type === "patient"){
            return $this->updateAppointmentInfo($request);
        }
    }

    private static function deleteCanceled(int $patientId){
        Appointment::where("patientId", "=", $patientId)
        ->whereNotNull("canceled_status_view_date")
        ->where("canceled_status_view_date", "<", now()->subDay()->format("Y-m-d"))
        ->delete();
    }

    private static function setCanceledViewDates(int $patientId){
        Appointment::where("patient_id", "=", $patientId)
        ->where("status", "=", false)
        ->update(["canceled_status_view_date" => now()]);
    } 
}

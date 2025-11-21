<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Helpers\Interfaces\IProfile;
use App\Http\Resources\Appointment\DoctorAppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use DateInterval;
use DateTime;
use Error;
use Illuminate\Database\Eloquent\Model;
use Psy\Readline\Hoa\Console;
use stdClass;

class Profile implements IProfile{


    #region helpers
    /**
     * preforms a ternary operation and returns the appropriate value if not null else the other.
     *
     * @param boolean $condition
     * @param mixed $value1
     * @param mixed $value2
     * @return void
     */
    private static function ternaryWithNullCheck(bool $condition, mixed $value1, mixed $value2){
        return $condition ? 
        ($value1 !== null ? $value1 : $value2) :
        ($value2 !== null ? $value2 : $value1);
    }
    #endregion


    #region constructor
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    #endregion

    #region properties
    private Model $model;
    #endregion

    /**
     * returns if a certain provider is open, using its schedule and taking into account
     * if it has updated its status manually.
     * cases :
     *  - last status update = null & schedule[day] => true.
     *  - schedule[day] = null:
     *      - last status update === date => status.
     *      else => true.
     *  else do calculations.
     *
     * @param string $dateTime the time to consider, format : "day/month/year hour:minutes"
     * @param Model $provider
     * @return boolean
     */
    public function isAvailable(string $dateTime = "") : bool{
        if ($dateTime === ""){
            $dateTime = date("d-m-Y H:i");
        }
        $dateTime = (new DateTime($dateTime))->format("d-m-Y H:i");
        [$date, $time] = explode(" ", $dateTime);
        $date = date("d-m-Y", strtotime($date));
        $day = strtolower(date("l", strtotime($dateTime)));

        if ($this->model->status_last_update === null && ($this->model->schedule === null || $this->model->schedule->{$day} === null)){
            return true;
        }

        $arr = $this->model->schedule->{$day};
        $arr = explode(";", $arr);
        $times = [];
        foreach ($arr as $item){
            $times = array_merge($times, explode("-", $item));
        }
        $isAvailable = false;
        for ($i = 0; $i < count($times); $i++){
            if ($times[$i] >= $time){
                break;
            }
            $isAvailable = !$isAvailable;
        }

        if ($this->model->status_last_update == null){
            return $isAvailable;
        }

        [$lastStatusUpdateDate, $lastStatusUpdateTime] = explode(" ", $this->model->status_last_update);
        $lastStatusUpdateDate = date("d-m-Y", strtotime($lastStatusUpdateDate));
        if ($this->model->schedule->{$day} === null){
            if ($lastStatusUpdateDate === $date){
                return $this->model->status;
            } else {
                return true;
            }
        }

        // check if the status update matches the specified date
        if ($date !== $lastStatusUpdateDate){
            return $isAvailable;
        }

        // check if the last status update is in interval
        $lastStatusUpdateTime = date("H:i", strtotime($lastStatusUpdateTime));
        if ($i === 0){
            return static::ternaryWithNullCheck($lastStatusUpdateTime < $times[0], $this->model->status, $isAvailable);
        } else if ($i === count($times)){
            return static::ternaryWithNullCheck($lastStatusUpdateTime > $times[$i - 1], $this->model->status, $isAvailable);
        } else {
            return static::ternaryWithNullCheck(
                $lastStatusUpdateTime <= $times[$i] && $lastStatusUpdateTime >= $times[$i - 1],
                $this->model->status,
                $isAvailable
            );
        }
    }

    public function getProfilePhoto() : string{
        if ($this->model->photo !== null){
            return $this->model->photo;
        }

        $photos = explode(";", $this->model->photos);
        foreach ($photos as $photo){
            if (str_contains($photo, PROFILE_PHOTO_INDICATOR . ":")){
                return explode(":", $photo)[1];
            }
        }
        return "";
    }

    public function getPhotosArray() : array|string|null{
        if ($this->model->photo !== null){
            return [
                "profile" => Photo::getPhotoAbsolutePath($this->model->photo),
            ];
        }

        if ($this->model->photos === null){
            return null;
        }

        $photosArray = ["profile" => "", "other" => []];
        foreach (explode(";", $this->model->photos) as $photo){
            if (str_contains($photo, PROFILE_PHOTO_INDICATOR . ":")){
                $photosArray["profile"] = Photo::getPhotoAbsolutePath(explode(PROFILE_PHOTO_INDICATOR . ":", $photo)[1]);
                continue;
            }
            $photosArray["other"][] = Photo::getPhotoAbsolutePath($photo);
        }
        return $photosArray;
    }

    public function getSocialsArray() : array|null{
        if ($this->model->socials === null){
            return null;
        }

        $socialsArray = [];
        foreach (explode(";", $this->model->socials) as $link){
            foreach (SOCIAL_NETWORK_FLAGS as $network => $flags){
                foreach ($flags as $flag){
                    if (str_contains($link, $flag)){
                        $socialsArray[] = [$network, $link];
                        break 2;
                    }
                }
            }
        }
        return $socialsArray;
    }

    private static function getDays(int $offset, int $number) : array{
        $today =(new DateTime("now"))->add(DateInterval::createFromDateString($offset . " days"));
        $days = [];
        for ($i = 0; $i < $number; $i++){
            $days[] = strtolower($today->add(DateInterval::createFromDateString($i === 0 ? "0 day" : "1 day"))->format("l"));
        }   
        return $days; 
    }

    private static function getDates(int $offset, int $number) : array{
        $today =(new DateTime("now"))->add(DateInterval::createFromDateString($offset . " days"));
        $dates = [];
        for ($i = 0; $i < $number; $i++){
            $dates[] = $today->add(DateInterval::createFromDateString($i === 0 ? "0 day" : "1 day"))->format("d-m-Y");
        }    
        return $dates;
    }

    private static function getTimeOffset(string $infTime, string $supTime, mixed $time) : float{
        $totalTimeDiff = strtotime($supTime) - strtotime($infTime);
        $timeToInfDiff = strtotime($time) - strtotime($infTime);
        return $timeToInfDiff / $totalTimeDiff;
    }

    private static function roundToHour(&$min, &$max){
        $min = (new DateTime($min))->format("H") . ":00";
        $maxDateTime = new DateTime($max);
        $max = (int)$maxDateTime->format("i") === 0 ? $max : ($maxDateTime->format("H") + 1) . ":00";
    }

    private function getWorkSchedule(int $offset, int $number, &$minTime, &$maxTime){
        $schedule = $this->model->schedule;
        if ($schedule === null || $number === 0){
            return null;
        }

        $days = static::getDays($offset, $number);
        $dates = static::getDates($offset, $number);
        $holder = [];
        for ($i = 0; $i < count($days); $i++){
            $day = $days[$i];
            $holder[$i] = $schedule->{$day} === null ? [] : array_map(fn($interval) => explode("-", $interval), explode(";", $schedule->{$day}));
        }

        $schedule = $holder;
        $min = "";
        $max = "";
        $scheduleObjects = [];
        foreach ($schedule as $number => $intervals){
            foreach ($intervals as $interval){
                $scheduleObj = [];
                foreach ($interval as $part){
                    if ($min === "" || $min > $part){
                        $min = $part;
                    } 
                    if ($max === "" || $max < $part){
                        $max = $part;
                    }
                }
                $scheduleObj["start"] = $interval[0];
                $scheduleObj["end"] = $interval[1];
                $scheduleObj["number"] = $number;
                $scheduleObj["date"] = $dates[$number];
                $scheduleObj["type"] = "schedule";
                $scheduleObjects[] = $scheduleObj;
            }
        }

        static::roundToHour($min, $max);

        $minTime = $min;
        $maxTime = $max;
        return $scheduleObjects;
    }

    private static function addScheduleOffsets(array &$scheduleObjects, string $minTime, string $maxTime){
        foreach ($scheduleObjects as &$obj){
            $obj["offsetStart"] = static::getTimeOffset($minTime, $maxTime, $obj["start"]);
            $obj["offsetEnd"] = static::getTimeOffset($minTime, $maxTime, $obj["end"]);
        }
    }

    private function getAppointmentSchedule(int $offset, int $number, &$minTime, &$maxTime){
        if ($number === 0){
            return null;
        }
        $dates = static::getDates($offset, $number);
        $appointmentsSchedulesIds = AppointmentSchedule::whereBetween("date", [$dates[0], $dates[count($dates) - 1]])->where("doctor_id", "=", $this->model->getKey())->pluck("schedule_id")->toArray();
        $appointments = Appointment::whereIn("schedule_id", $appointmentsSchedulesIds)->get();
        if ($appointments === null || $appointments->count() === 0){
            return null;
        }
        $appointmentSchedules = [];
        $min = "";
        $max = "";
        foreach ($appointments as $appointment){
            $appointmentScheduleDisplay = [];
            $schedule = $appointment->schedule;
            $appointmentTimeInterval = AppointmentController::getInterval(
                $appointment->session_number,
                $schedule->interval,
                $schedule->appointments_number
            );

            foreach ($appointmentTimeInterval as $part){
                if ($min === "" || $min > $part){
                    $min = $part;
                }
                if ($max === "" || $max < $part){
                    $max = $part;
                }
            }

            $appointmentScheduleDisplay["start"] = $appointmentTimeInterval[0];
            $appointmentScheduleDisplay["end"] = $appointmentTimeInterval[1];
            $appointmentScheduleDisplay["date"] = $schedule->date;
            $appointmentScheduleDisplay["number"] = array_search($schedule->date, $dates);
            $appointmentScheduleDisplay["type"] = "appointment";
            $appointmentScheduleDisplay["appointment"] = new DoctorAppointmentResource($appointment);
            $appointmentSchedules[] = $appointmentScheduleDisplay; 
        } 

        static::roundToHour($min, $max);
        $minTime = $min;
        $maxTime = $max;
        return $appointmentSchedules;
    }

    private static function getHours(string $inf, string $sup){
        $time = new DateTime($inf);
        $hours = [$inf];
        $hour = $inf;
        while (true){
            $hour = $time->add(DateInterval::createFromDateString("1 hour"))->format("H:i");
            if ($hour > $sup || ($sup === "23:00" && $hour === "00:00")){
                break;
            }
            $hours[] = $hour;
        }

        return $hours;
    } 

    /**
     * get the schedule of a model as-is(non doctor), or with appointments(doctor)
     *
     * @param integer $offset the number of days from to day to start the schedule from
     * @param integer $number the number of days to get
     * @param bool $withAppointments get the doctor schedule with his appointments
     * @return array|null 
     */
    public function getDisplaySchedule(int $offset = 0, int $number = 5, bool $withAppointments = true) : array|null{
        $minWork = "";
        $maxWork = "";
        $workSchedule = $this->getWorkSchedule($offset, $number, $minWork, $maxWork);
        if ($workSchedule === null){
            return null;
        }
        $result = [];
        if (!($this->model instanceof \App\Models\Doctor) || ($this->model instanceof \App\Models\Doctor && !$withAppointments)){
            static::addScheduleOffsets($workSchedule, $minWork, $maxWork);
            $result["hours"] = static::getHours($minWork, $maxWork);
            $result["schedules"] = $workSchedule;
            return $result;
        }
        $minAppointment = "";
        $maxAppointment = "";
        $appointmentsSchedule = $this->getAppointmentSchedule($offset, $number, $minAppointment, $maxAppointment);
        if ($appointmentsSchedule === null){
            return null;
        }
        $min = min($minAppointment, $minWork);
        $max = max($maxAppointment, $maxWork);
        static::addScheduleOffsets($workSchedule, $min, $max);
        static::addScheduleOffsets($appointmentsSchedule, $min, $max);
        $result["hours"] = static::getHours($min, $max);
        $result["schedules"] = array_merge($appointmentsSchedule, $workSchedule);
        return $result;
    }

    public function getFilterValues(string $columnName){
        if ($this->model->{$columnName} === null){
            return null;
        }

        return $this->model->{$columnName}->pluck("name")->toArray();
    }

    public function addFilterValues(array $filters, array &$values){
        foreach ($filters as $filter){
            $values[$filter] = $this->getFilterValues($filter); 
        }
    }
}
<?php

namespace App\Http\Controllers\Helpers\Interfaces;

interface IProfile{

    /**
     * returns if a certain provider is open, using its schedule and taking into account
     * if it has updated its status manually.
     *
     * @param string $dateTime the time to consider, format : "day/month/year hour:minutes"
     * @param Model $provider
     * @return boolean
     */
    function isAvailable(string $dateTime);

    /**
     * return the profile photo
     *
     * @return void
     */
    function getProfilePhoto();

    /**
     * return all photos as
     * profile => profile photo
     * other => array(showcase photos)
     *
     * @return void
     */
    function getPhotosArray();

    /**
     * get the social networks of the profile with there type
     *
     * @return void
     */
    function getSocialsArray();

    /**
     * get the schedule of a model as-is(non doctor), or with appointments(doctor)
     *
     * @param integer $offset the number of days from to day to start the schedule from
     * @param integer $number the number of days to get
     * @param bool $withAppointments get the doctor schedule with his appointments
     * @return void
     */
    function getDisplaySchedule(int $offset, int $number, bool $withAppointments);

}
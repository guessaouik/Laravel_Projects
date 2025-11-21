<?php 

namespace App\Traits;
include_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'constants.php';
include_once AUTOLOADER;

use App\Models\Schedule;
use Relationship\Morph;

trait HasSchedule{

    public function schedule(){
        return Morph::hasOneThroughMorph(
            $this,
            Schedule::class,
            "provider_schedule",
            "provider_type",
            "provider_id",
            "schedule_id"
        );
    }
}
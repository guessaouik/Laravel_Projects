<?php

use Illuminate\Database\Eloquent\Model;

define("ROOT_DIR",  dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("AUTOLOADER", ROOT_DIR . "helpers" . DIRECTORY_SEPARATOR . "autoload.php");
define("TABLEFILLER",  ROOT_DIR . "helpers" . DIRECTORY_SEPARATOR. "TableFiller.php");
define('TABLE_SAMPLE_NUMBER', 10);
define('SECONDARY_TABLE_SAMPLE_NUMBER', 30);
define('TABLES_WITH_SCHEDULE', 4);
define('TABLES_WITH_NOTIFY', 7);
define('SCHEDULE_SAMPLE_NUMBER', TABLE_SAMPLE_NUMBER * TABLES_WITH_SCHEDULE);
define('NOTIFICATION_SAMPLE_NUMBER', TABLE_SAMPLE_NUMBER * TABLES_WITH_NOTIFY);
define('DEFAULT_PER_PAGE', 12);

define("MODEL_ALIAS", [
    "App\\Models\\Doctor"    => "d",
    "App\\Models\\Patient"   => "pa",
    "App\\Models\\Hospital"  => "h",
    "App\\Models\\Clinic"    => "c",
    "App\\Models\\Pharmacy"  => "ph",
    "App\\Models\\MIC"       => "m",
    "App\\Models\\Lab"       => "l"
]);

define("TYPE_ALIAS", [
    "doctor"    => "d",
    "patient"   => "pa",
    "hospital"  => "h",
    "clinic"    => "c",
    "pharmacy"  => "ph",
    "mic"       => "m",
    "lab"       => "l"
]);

define("TYPE_MODEL", [
    "hospital"  => "App\\Models\\Hospital",
    "clinic"    => "App\\Models\\Clinic",
    "pharmacy"  => "App\\Models\\Pharmacy",
    "doctor"    => "App\\Models\\Doctor",
    "mic"       => "App\\Models\\MIC",
    "lab"       => "App\\Models\\Lab",
    "patient"   => "App\\Models\\Patient"
]);

define("ATTRIBUTE_REQUEST_KEY",[
    "rating" => fn(Model $model) => $model->rating,
    "views" => fn(Model $model) => $model->views,
    "time" => fn(Model $model) => $model->updated_at,
    "name" => fn(Model $model) => $model->name ?? $model->firstname . " " . $model->lastname,
]);

define("PROFILE_PHOTO_INDICATOR", "PROFILE");

define("SOCIAL_NETWORK_FLAGS", [
    "instagram" => ["instagram"],
    "facebook" => ["facebook"],
    "linkedin" => ["linkedin"],
    "reddit" => ["reddit"],
    "gmail" => ["mail.google"],
    "snapchat" => ["snapchat"],
    "tiktok" => ["tiktok"],
    "twitter" => ["twitter"],
    "whatsapp" => ["whatsapp"],
]);

define("TYPE_USER_TYPE", [
    "patient" => "PATIENT",
    "doctor" => "DOCTOR",
    "lab" => "LAB",
    "mic" => "MIC",
    "hospital" => "HOSPITAL",
    "clinic" => "CLINIC",
    "pharmacy" => "PHARMACY",
]);

define("SCHEDULE_DAYS_DEFAULT_NUMBER", 5);
define("SCHEDULE_DAYS_DEFAULT_OFFSET", 0);

define("HOME_RESULTS_NUMBER", 3);

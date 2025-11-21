<?php

namespace Database\Helpers;
/*
// tell the migration script to skip the file
// connection info
$username = env("DB_USERNAME");
$password = env("DB_PASSWORD");
$conn = mysqli_connect("localhost", $username, $password, env("DB_DATABASE"));
// add TableFiller to migration table
$sql = "SELECT id FROM migrations WHERE migration = 'TableFiller';";
$result = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if ($result === null || count($result) == 0){
    $sql = "INSERT INTO migrations (migration, batch) VALUES ('TableFiller', 0);";
    mysqli_query($conn, $sql);
}

mysqli_close($conn);
*/

use Illuminate\Database\Schema\Blueprint;

class TableFiller{

    /**
     * helps set recurring columns of medical provider tables
     * 
     * this columns are : (longitude, latitude, address, city_id, photo(s), phone, socials, about)
     *      longitude:  double  ****.**** (4.4)
     *      latitude:   double  ****.**** (4.4)
     *      address:    string  [nullable]
     *      city_id:    integer
     *      photo(s):   string  [nullable]
     *      phone:      integer [nullable](if hasPhone)
     *      socials:    string  [nullable]
     *      about:      string  [nullable]
     */

    public static function setMedicalProviderColumns(
        Blueprint &$table, bool $hasPhotos = true 
        ) : void{
            $table->double("longitude", 9, 4)->nullable();
            $table->double("latitude", 9, 4)->nullable();
            $table->string("address")->nullable()->default("");
            $table->string("photo" . (($hasPhotos) ? "s" : ""), 1000)->nullable()->default("");
            $table->string("socials", 1000)->nullable()->default("");
            $table->string("about")->nullable()->default("");
    }

    /**
     * helps set recurring columns that store personal info of patient/doctor tables
     * 
     * this columns are : (firstname, lastname, email, phone, password)
     *      firstname:  string
     *      lastname:   string
     *      email:      string  [unique]
     *      phone:      integer [unique]
     *      password:   string
     */
    public static function setPersonInfoColumns(Blueprint &$table) : void{
        $table->string("firstname");
        $table->string("lastname");
        $table->string("email")->unique()->nullable();
        $table->string("phone")->nullable();
        $table->string("password");
    }

    /**
     * helps set recurring columns that store personal info of entities tables
     * 
     * this columns are : (username, name, password)
     *      username:   string [unique]
     *      name:       string
     *      password:   string 
     */
    public static function setEntityInfoColumns(Blueprint &$table){
        $table->string("name");
        $table->string("email")->unique()->nullable();
        $table->string("phone")->unique()->nullable();
        $table->string("password");
    }

    public static function addForeign(
        Blueprint &$table,
        string $tableName,
        string $primary,
        string $colName
    ){
        $table->foreign($colName)
                    ->references($primary)
                    ->on($tableName)
                    ->onUpdate("cascade")
                    ->onDelete("cascade");
    }


    public static function addReferenceColumn(
        Blueprint &$table,
        string $tableName,
        string $primary,
        string $colName
    ){
        $table->bigInteger($colName)->unsigned();
        static::addForeign($table, $tableName, $primary, $colName);
    }

    public static function setPivotColumns(
        Blueprint &$table,
        string $table1,
        string $table2,
        string $primary1,
        string $primary2,
        string $colName1,
        string $colName2
    ){
        static::addReferenceColumn($table, $table1, $primary1, $colName1);
        static::addReferenceColumn($table, $table2, $primary2, $colName2);
    }

    /**
     * helps set recurring columns that store profile info 
     * 
     * this columns are : (schedule, views, rating, status, status_last_update)
     *      schedule :          string      [nullable]
     *      views:              integer     [default = 0]
     *      rating:             float       [default = 0]
     *      status:             boolean     [default = false]
     *      status_last_update: datetime    [nullable]
     */
    public static function setProfileInfoColumns(
            Blueprint &$table, $hasSchedule = true, $hasStatusUpdate = true
        ){
        if ($hasSchedule){
            //$table->string("schedule")->nullable();
        }
        $table->integer("views")->default(0);
        $table->double("rating", 3, 1)->default(5);
        $table->boolean("status")->nullable()->default(false); // true : (open / available), false : (closed / not available), null : unknown
        if ($hasStatusUpdate){
            $table->timestamp("status_last_update")->nullable();
        }
    }

    public static function setSimpleMorphPivot(
        Blueprint &$table,
        string $singularTableName,
        string $singularPrimary,
        string $singularColName,
        string $prefix,
        bool $hasTimestamps = true
    ){
        TableFiller::addReferenceColumn($table, $singularTableName, $singularPrimary, $singularColName);
        $table->string("{$prefix}_type", 2);
        $table->bigInteger("{$prefix}_id")->unsigned();
        if ($hasTimestamps){
            $table->timestamps();
        }
    }

    public static function setComplexMorphPivot(
        Blueprint &$table,
        string $prefix1,
        string $prefix2,
        bool $hasTimestamps = true
    ){
        $table->string("{$prefix1}_type", 2);
        $table->bigInteger("{$prefix1}_id")->unsigned();
        $table->string("{$prefix2}_type", 2);
        $table->bigInteger("{$prefix2}_id")->unsigned();
        if ($hasTimestamps){
            $table->timestamps();
        }
    }

}
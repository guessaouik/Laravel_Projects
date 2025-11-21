<?php

// delete after done

namespace Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Utils{

    public static function truncateAllTables(){
        $excludes = [
            "privileges", "services", "specialties", "cities", "states", "treatments", "diseases",
            "medical_equipments", "tests", "technologies", "migrations"
        ];
        Schema::disableForeignKeyConstraints();
        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tableNames as $name) {
            if (in_array($name, $excludes)) {
                continue;
            }
            DB::table($name)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }
}
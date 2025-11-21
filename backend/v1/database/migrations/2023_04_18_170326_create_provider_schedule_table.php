<?php
include_once TABLEFILLER;

use Database\Helpers\TableFiller;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_schedule', function (Blueprint $table) {
            TableFiller::setSimpleMorphPivot($table, "schedules", "schedule_id", "schedule_id", "provider");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_schedule');
    }
};

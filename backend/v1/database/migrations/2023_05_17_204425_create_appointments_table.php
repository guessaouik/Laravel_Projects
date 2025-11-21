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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id("appointment_id");
            TableFiller::addReferenceColumn($table, "patients", "patient_id", "patient_id");
            $table->string("info")->nullable();
            TableFiller::addReferenceColumn($table, "appointment_schedules", "schedule_id", "schedule_id");
            $table->unsignedTinyInteger("session_number")->nullable();
            $table->boolean('status')->nullable(); // true : approved, false : cancelled, null : no response
            $table->date("canceled_status_view_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

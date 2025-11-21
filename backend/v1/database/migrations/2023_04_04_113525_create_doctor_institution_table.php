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
        Schema::create('doctor_institution', function (Blueprint $table) {
            TableFiller::setSimpleMorphPivot($table, "doctors", "doctor_id", "doctor_id", "institution");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_institution');
    }
};

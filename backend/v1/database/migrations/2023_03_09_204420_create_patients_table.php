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
        Schema::create('patients', function (Blueprint $table) {
            $table->id("patient_id");
            TableFiller::setPersonInfoColumns($table);
            $table->string("address")->nullable();
            $table->string("photo")->nullable();
            $table->string("socials", 1000)->nullable();
            $table->timestamp("birth_date")->nullable();
            $table->string("gender", 1);
            $table->string("blood_type", 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

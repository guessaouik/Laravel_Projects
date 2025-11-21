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
        Schema::create('m_i_c_s', function (Blueprint $table) {
            $table->id("mic_id");
            TableFiller::setEntityInfoColumns($table);
            TableFiller::setMedicalProviderColumns($table);
            TableFiller::setProfileInfoColumns($table, false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_i_c_s');
    }
};

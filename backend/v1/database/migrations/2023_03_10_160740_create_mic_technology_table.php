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
        Schema::create('mic_technology', function (Blueprint $table) {
            TableFiller::setPivotColumns($table, "m_i_c_s", "technologies", "mic_id", "technology_id", "mic_id", "technology_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mic_technology');
    }
};

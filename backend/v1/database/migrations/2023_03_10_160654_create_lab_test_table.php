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
        Schema::create('lab_test', function (Blueprint $table) {
            TableFiller::setPivotColumns($table, 'labs', 'tests', 'lab_id', 'test_id', "lab_id", "test_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_test');
    }
};

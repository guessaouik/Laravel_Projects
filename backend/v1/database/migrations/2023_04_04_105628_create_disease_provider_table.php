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
        Schema::create('disease_provider', function (Blueprint $table) {
            TableFiller::setSimpleMorphPivot($table, "diseases", "disease_id", "disease_id", "provider");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_provider');
    }
};

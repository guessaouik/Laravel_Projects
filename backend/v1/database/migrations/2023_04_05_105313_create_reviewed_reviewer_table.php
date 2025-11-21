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
        Schema::create('reviewed_reviewer', function (Blueprint $table) {
            TableFiller::setComplexMorphPivot($table, "reviewed", "reviewer", false);
            TableFiller::addReferenceColumn($table, "reviews", "review_id", "review_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviewed_reviewer');
    }
};

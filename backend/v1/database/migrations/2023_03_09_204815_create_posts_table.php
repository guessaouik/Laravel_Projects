<?php

include_once TABLEFILLER;

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
        Schema::create('posts', function (Blueprint $table) {
            $table->id("post_id");
            $table->integer("parent_id")->default(0);
            $table->string("title", 100)->nullable();
            $table->string("photo", 1000)->nullable();
            $table->string("content", 800);
            $table->integer("likes")->unsigned()->default(0);
            $table->integer("replies")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

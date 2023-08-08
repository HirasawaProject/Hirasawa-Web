<?php

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
        Schema::create('beatmap_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beatmap_id');
            $table->foreign('beatmap_id')->references('id')->on('beatmaps');
            $table->integer('mode');
            $table->integer('play_count');
            $table->integer('pass_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beatmap_stats');
    }
};

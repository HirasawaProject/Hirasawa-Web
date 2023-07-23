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
        Schema::create('beatmap_sets', function (Blueprint $table) {
            $table->id();
            $table->integer('osu_id');
            $table->string('artist');
            $table->string('title');
            $table->integer('status');
            $table->string('mapper_name');
            $table->integer('genre_id');
            $table->integer('language_id');
            $table->float('rating');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beatmapsets');
    }
};

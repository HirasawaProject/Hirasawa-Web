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
        Schema::create('beatmaps', function (Blueprint $table) {
            $table->id();
            $table->integer('osu_id')->unique();
            $table->unsignedBigInteger('beatmap_set_id');
            $table->foreign('beatmap_set_id')->references('id')->on('beatmap_sets');
            $table->string('difficulty_name');
            $table->string('hash', 32);
            $table->integer('ranks');
            $table->float('offset');
            $table->integer('total_length');
            $table->integer('hit_length');
            $table->float('circle_size');
            $table->float('overall_difficulty');
            $table->float('approach_rate');
            $table->float('health_drain');
            $table->integer('gamemode');
            $table->integer('count_normal');
            $table->integer('count_slider');
            $table->integer('count_spinner');
            $table->float('bpm');
            $table->boolean('has_storyboard');
            $table->integer('max_combo');
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
        Schema::dropIfExists('beatmaps');
    }
};

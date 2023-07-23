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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('score');
            $table->integer('combo');
            $table->integer('count_50');
            $table->integer('count_100');
            $table->integer('count_300');
            $table->integer('count_miss');
            $table->integer('count_katu');
            $table->integer('count_geki');
            $table->boolean('full_combo');
            $table->integer('mods');
            $table->integer('timestamp');
            $table->unsignedBigInteger('beatmap_id');
            $table->integer('gamemode');
            $table->integer('rank');
            $table->float('accuracy');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('beatmap_id')->references('id')->on('beatmaps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};

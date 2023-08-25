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
        Schema::table('user_stats', function (Blueprint $table) {
            $table->integer('pass_count')->default(0)->after('play_count');
            $table->integer('total_hits')->default(0)->after('pass_count');
            $table->integer('max_combo')->default(0)->after('total_hits');
            $table->integer('replays_watched')->default(0)->after('max_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_stats', function (Blueprint $table) {
            $table->dropColumn('pass_count');
            $table->dropColumn('total_hits');
            $table->dropColumn('max_combo');
            $table->dropColumn('replays_watched');
        });
    }
};

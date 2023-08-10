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
        Schema::table('beatmaps', function (Blueprint $table) {
            $table->dropColumn('play_count');
            $table->dropColumn('pass_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beatmaps_tables', function (Blueprint $table) {
            $table->integer('play_count')->default(0);
            $table->integer('pass_count')->default(0);
        });
    }
};

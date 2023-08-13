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
            $table->float('accuracy')->default(0)->change();
            $table->integer('play_count')->default(0)->change();
            $table->bigInteger('total_score')->default(0)->change();
            $table->bigInteger('ranked_score')->default(0)->change();
            $table->integer('rank')->default(0)->change();
            $table->smallInteger('pp')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_stats', function (Blueprint $table) {
            $table->float('accuracy')->default(null)->change();
            $table->integer('play_count')->default(null)->change();
            $table->bigInteger('total_score')->default(null)->change();
            $table->bigInteger('ranked_score')->default(null)->change();
            $table->integer('rank')->default(null)->change();
            $table->smallInteger('pp')->default(null)->change();
        });
    }
};

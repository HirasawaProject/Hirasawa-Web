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
            $table->integer('osu_ranks')->default(0)->after('pass_count');
            $table->integer('taiko_ranks')->default(0)->after('osu_ranks');
            $table->integer('ctb_ranks')->default(0)->after('taiko_ranks');
            $table->integer('mania_ranks')->default(0)->after('ctb_ranks');
            $table->dropColumn('ranks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beatmaps', function (Blueprint $table) {
            $table->dropColumn('osu_ranks');
            $table->dropColumn('taiko_ranks');
            $table->dropColumn('ctb_ranks');
            $table->dropColumn('mania_ranks');
            $table->integer('ranks')->default(0)->after('hash');
        });
    }
};

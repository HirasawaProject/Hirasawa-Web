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
        Schema::create('permission_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('node', 255);
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('permission_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_nodes');
    }
};

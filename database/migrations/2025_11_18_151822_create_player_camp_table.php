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
        Schema::create('Player_Camp', function (Blueprint $table) {
            // Primary key
            $table->id('player_camp_id');

            // Foreign Keys
            $table->unsignedBigInteger('Camp_ID')->nullable();
            $table->unsignedBigInteger('Player_ID')->nullable();

            // Foreign Key constraints
            $table->foreign('Player_ID')->references('Player_ID')->on('players')->onDelete('cascade');
            $table->foreign('Camp_ID')->references('Camp_ID')->on('camps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Player_Camp');
    }
};
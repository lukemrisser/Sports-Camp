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
        Schema::create('Teammate_Request', function (Blueprint $table) {

            // Primary Key
            $table->id('Teammate_Request_ID');
            
            // Data Columns
            $table->unsignedBigInteger('Player_ID')->nullable();

            $table->string('Requested_FirstName', 50)->nullable();
            $table->string('Requested_LastName', 50)->nullable();
            
            $table->unsignedBigInteger('Camp_ID')->nullable();

            // Foreign Key Constraints
            $table->foreign('Player_ID')->references('Player_ID')->on('players')->onDelete('cascade');
            $table->foreign('Camp_ID')->references('Camp_ID')->on('camps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Teammate_Request');
    }
};

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
        Schema::create('Players', function (Blueprint $table) {
            // Primary Key
            $table->id('Player_ID');

            // Foreign Key
            $table->unsignedBigInteger('Parent_ID');

            $table->string('Camper_FirstName', 50);
            $table->string('Camper_LastName', 50);
            $table->string('Gender', 10);
            $table->date('Birth_Date');
            $table->string('Shirt_Size', 50);
            $table->string('Allergies')->nullable();
            $table->boolean('Asthma')->default(false);
            $table->string('Medication_Status')->nullable();
            $table->string('Injuries')->nullable();
            
            // Add foreign key constraint
            $table->foreign('Parent_ID')->references('Parent_ID')->on('parents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};

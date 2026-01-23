<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Coaches', function (Blueprint $table) {
            // Renamed PK to match SQL dump
            $table->id('Coach_ID'); 
            
            // Added missing columns from SQL dump
            $table->string('Coach_FirstName', 50)->nullable();
            $table->string('Coach_LastName', 50)->nullable();
            
            // Adjusted FK definition
            $table->unsignedBigInteger('user_id')->unique()->nullable();
            $table->boolean('admin')->nullable();
            $table->string('sport', 255)->nullable();

            // Foreign Key constraint based on SQL dump
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Coaches');
    }
};
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
        Schema::create('parents', function (Blueprint $table) {
            $table->id('Parent_ID');
            

            // Table fields
            $table->string('Parent_FirstName', 50);
            $table->string('Parent_LastName', 50);
            $table->string('Address', 255);
            $table->string('City', 100);
            $table->string('State', 50);
            $table->string('Postal_Code', 25);
            $table->string('Email', 255)->unique();
            $table->string('Phone', 50);
            $table->string('Church_Name', 255)->nullable();
            $table->string('Church_Attendance', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
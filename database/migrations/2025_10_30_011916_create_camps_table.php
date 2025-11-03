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
        Schema::create('camps', function (Blueprint $table) {
            $table->id('Camp_ID');
            $table->string('Camp_Name', 255);
            $table->text('Description')->nullable();
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->timestamp('Registration_Open')->nullable();
            $table->timestamp('Registration_Close')->nullable();
            $table->decimal('Price', 8, 2);
            $table->string('Camp_Gender', 10);
            $table->unsignedSmallInteger('Age_Min');
            $table->unsignedSmallInteger('Age_Max');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camps');
    }
};

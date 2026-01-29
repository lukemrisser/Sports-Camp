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
            $table->string('Camp_Name', 50)->nullable();
            $table->text('Description')->nullable();
            $table->date('Start_Date')->nullable();
            $table->date('End_Date')->nullable();
            $table->date('Registration_Open')->nullable();
            $table->date('Registration_Close')->nullable();
            $table->decimal('Price', 10, 2)->nullable();
            $table->enum('Camp_Gender', ['girls', 'boys', 'coed']);
            $table->tinyInteger('Age_Min')->nullable();
            $table->tinyInteger('Age_Max')->nullable();
            $table->integer('Sport_ID')->nullable();
            $table->integer('Max_Capacity')->nullable();
            $table->string('Location_Name', 255)->nullable();
            $table->string('Street_Address', 255)->nullable();
            $table->string('City', 100)->nullable();
            $table->string('State', 50)->nullable();
            $table->string('Zip_Code', 20)->nullable();
            
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

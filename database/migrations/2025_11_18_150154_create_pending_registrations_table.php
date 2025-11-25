<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->string('token')->unique();
            $table->json('additional_data')->nullable(); // For coach-specific data
            $table->timestamp('expires_at');
            $table->timestamps(); // KEEP timestamps() as they are present in the SQL dump structure

            // Add index for faster token lookups
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
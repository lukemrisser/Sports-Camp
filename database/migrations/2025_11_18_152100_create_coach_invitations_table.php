<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('name');
            $table->string('token')->unique();
            $table->timestamps();

            // Allow multiple invitations to the same email for testing/re-invites
            // index on email for lookups
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_invitations');
    }
};

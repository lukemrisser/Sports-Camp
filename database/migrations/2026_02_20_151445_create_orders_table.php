<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Using 'Orders' (capitalized) because your error log specifically 
        // shows the query failed looking for "Orders".
        Schema::create('Orders', function (Blueprint $table) {
            $table->id('Order_ID'); 
            $table->integer('Parent_ID'); 
            $table->integer('Camp_ID');
            $table->dateTime('Order_Date')->nullable();
            $table->decimal('Item_Amount', 10, 2)->default(0);
            $table->decimal('Item_Amount_Paid', 10, 2)->default(0);
            $table->integer('Player_ID');
            $table->decimal('Refund_Amount', 10, 2)->default(0);
            $table->string('Payment_Intent_ID')->nullable();
            $table->string('Charge_ID')->nullable();
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Orders');
    }
};
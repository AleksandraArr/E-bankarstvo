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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_account')->constrained('accounts', 'id')->onDelete('cascade');  
            $table->foreignId('receiver_account')->constrained('accounts', 'id')->onDelete('cascade');  
            $table->foreignId('category_id')->constrained('transaction_categories', 'id')->onDelete('cascade');
            $table->dateTime('date');  
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

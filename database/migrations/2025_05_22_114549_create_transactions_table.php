<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); 
            $table->decimal('amount', 10, 2); 
            $table->enum('type', ['income', 'expense']); 
            $table->string('category')->nullable(); 
            $table->string('payment_type')->nullable(); 
            $table->date('date'); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
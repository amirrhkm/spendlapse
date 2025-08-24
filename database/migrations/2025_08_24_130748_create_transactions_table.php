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
            $table->date('transaction_date');
            $table->text('transaction_details');
            $table->decimal('money_in', 10, 2)->nullable();
            $table->decimal('money_out', 10, 2)->nullable();
            $table->decimal('balance', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->timestamps();
            
            $table->index('transaction_date');
            $table->index(['transaction_date', 'balance']);
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

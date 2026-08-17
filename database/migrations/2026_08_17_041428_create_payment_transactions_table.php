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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('provider_transaction_id')->nullable()->index();
            $table->unsignedBigInteger('amount_tiyin');
            $table->string('state')->default('created');
            $table->timestamp('performed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedTinyInteger('cancel_reason')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

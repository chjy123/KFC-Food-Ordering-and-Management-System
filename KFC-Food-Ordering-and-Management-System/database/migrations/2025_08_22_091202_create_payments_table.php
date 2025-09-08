<?php
#author’s name： Pang Jun Meng
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method');
            $table->string('payment_status')->default('Pending');
            $table->dateTime('payment_date')->nullable();
            $table->decimal('amount', 10, 2);

            // operational (safe) fields
            $table->string('transaction_ref')->nullable(); // Stripe session/payment_intent id
            $table->string('card_brand')->nullable();
            $table->string('card_last4', 4)->nullable();

            // idempotency to prevent duplicate charges on our side
            $table->uuid('idempotency_key')->nullable()->unique();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};



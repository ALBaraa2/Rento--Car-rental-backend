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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // payer

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            $table->string('provider'); // e.g., paypal, stripe, etc.
            $table->string('payment_method')->nullable(); // e.g., card, wallet, bank
            $table->enum('status', [
                'initiated',
                'requires_action',
                'authorized',
                'captured',
                'failed',
                'cancelled',
                'refunded',
            ])->default('initiated');

            // External identifiers from gateways
            $table->string('transaction_id')->nullable()->unique(); // PayPal capture ID or equivalent
            $table->string('order_id')->nullable()->unique(); // PayPal order ID or equivalent
            $table->string('payment_intent_id')->nullable()->unique(); // Stripe intent ID or equivalent

            // Optional details
            $table->string('receipt_url')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();

            // Lifecycle timestamps
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['provider']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

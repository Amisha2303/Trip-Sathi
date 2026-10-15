<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payments, gateway transactions and refunds.
 *
 * A booking may have several payment attempts; the PaymentService serialises
 * capture inside a transaction with a row lock on the booking so two concurrent
 * callbacks cannot both mark it paid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('gateway', 20)->default('offline')->index();
            $table->string('transaction_reference')->nullable()->unique();
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_refunded')->default(0);
            $table->string('currency', 10)->default('INR');
            $table->text('payload')->nullable();     // gateway response (never credentials)
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status'], 'payments_booking_status_index');
            $table->index(['user_id', 'status'], 'payments_user_status_index');
            $table->softDeletes();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('gateway', 20)->index();
            $table->string('reference')->nullable()->index();
            $table->string('type', 30)->default('charge'); // charge|refund|verify
            $table->string('status', 30)->default('initiated')->index();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('INR');
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'reference']);
            $table->softDeletes();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('gateway', 20)->default('offline');
            $table->string('reference')->nullable()->index();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('INR');
            $table->string('status', 30)->default('pending')->index(); // pending|succeeded|failed
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
    }
};

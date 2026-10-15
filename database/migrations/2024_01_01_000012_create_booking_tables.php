<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The reusable booking engine.
 *
 * One `bookings` row is the invoice header (money, status, lifecycle); each
 * `booking_items` row is a line item pointing at a polymorphic bookable record.
 * A single booking may therefore mix a hotel room and a darshan slot, which is
 * what the trip planner needs when a traveller books "the whole weekend".
 *
 * `bookable` on the header is populated for the common single-item case so the
 * admin booking list can filter and sort without touching line items.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 32)->unique();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->foreignId('trip_id')->nullable();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            $table->string('booking_type', 40)->index();

            // Single-item fast path (matches booking_items for most bookings).
            $table->nullableMorphs('bookable');

            // Customer snapshot — invoices must stay correct if the profile changes.
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();

            $table->date('booking_date')->index();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('nights')->default(1);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('guests')->default(1);

            // Money. All amounts are stored in the smallest currency unit of the
            // configured currency (paise for INR) to avoid float drift.
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('final_amount')->default(0);
            $table->string('currency', 10)->default('INR');

            $table->string('payment_status', 30)->default('pending')->index();
            $table->string('status', 30)->default('pending')->index();

            $table->boolean('cancelled')->default(false)->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->unsignedBigInteger('refunded_amount')->default(0);

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('source', 20)->default('web')->index(); // web|api|admin

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status'], 'bookings_user_status_index');
            $table->index(['booking_type', 'booking_date'], 'bookings_type_date_index');
            $table->index(['created_at', 'status'], 'bookings_recent_index');
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('item_type', 40);       // mirrors booking_type for the line
            $table->nullableMorphs('itemable');    // hotel|room|restaurant|event|adventure|darshan|pooja...
            $table->foreignId('slot_id')->nullable();
            $table->string('slot_table', 40)->nullable(); // adventure_slots|darshan_slots|pooja_slots|restaurant_slots

            $table->string('name');               // snapshot at purchase time
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->date('service_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('guests')->default(1);
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);

            $table->json('options')->nullable();  // selected room, ticket type, meal preference
            $table->json('visitor_details')->nullable(); // VIP darshan ID details
            $table->timestamps();

            $table->index(['booking_id']);
            $table->index(['itemable_type', 'itemable_id'], 'booking_items_itemable_index');
            $table->index(['service_date'], 'booking_items_service_date_index');
        });

        // Created after bookings so the foreign key can point at it.
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->index(['coupon_id', 'user_id'], 'coupon_usages_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};

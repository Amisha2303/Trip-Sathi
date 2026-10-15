<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trip planner.
 *
 * `trip_days` materialises one row per calendar day so the itinerary can be
 * reordered with a plain UPDATE (sort_order) inside a transaction, and
 * `trip_items` hangs off days — but also carries `day_number` so a single query
 * can fetch a flattened itinerary without a join in the view layer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('total_days')->default(1);
            $table->unsignedInteger('travelers')->default(1);
            $table->unsignedBigInteger('budget')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->text('notes')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_public')->default(false)->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->boolean('is_completed')->default(false)->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'start_date'], 'trips_user_start_index');
            $table->index(['is_public', 'start_date'], 'trips_public_index');
        });

        Schema::create('trip_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number');
            $table->date('date');
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['trip_id', 'day_number']);
        });

        Schema::create('trip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('trip_day_id')->nullable()->constrained('trip_days')->nullOnDelete();
            $table->unsignedTinyInteger('day_number')->default(1)->index();

            $table->string('item_type', 40)->index();
            $table->nullableMorphs('itemable');

            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            $table->string('title');                 // falls back to the catalogue name
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('currency', 10)->default('INR');
            $table->json('meta')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['trip_id', 'day_number', 'sort_order'], 'trip_items_itinerary_index');
            $table->index(['itemable_type', 'itemable_id'], 'trip_items_itemable_index');
        });

        // Bookings can be attached to a trip once they exist.
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('trip_id')->references('id')->on('trips')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
        });

        Schema::dropIfExists('trip_items');
        Schema::dropIfExists('trip_days');
        Schema::dropIfExists('trips');
    }
};

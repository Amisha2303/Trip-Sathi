<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * VIP Darshan — a dedicated slot-based product. `darshan_slots` carries the
 * per-slot visitor capacity that the booking engine locks before confirming.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('darshan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            $table->string('temple_name');
            $table->string('deity', 100)->nullable();
            $table->string('darshan_type', 40)->default('vip')->index(); // vip|regular|special|online
            $table->string('temple_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->unsignedTinyInteger('max_visitors_per_booking')->default(10);
            $table->unsignedSmallInteger('max_visitors_per_day')->nullable();

            $table->text('instructions')->nullable();
            $table->json('documents_required')->nullable(); // ["Photo ID", "Aadhaar", ...]
            $table->text('dress_code')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->string('main_image')->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'is_featured'], 'darshan_listing_index');
            $table->index(['temple_name', 'darshan_type'], 'darshan_temple_index');
        });

        Schema::create('darshan_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('darshan_id')->constrained('darshan')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['darshan_id', 'sort_order']);
        });

        Schema::create('darshan_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('darshan_id')->constrained('darshan')->cascadeOnDelete();
            $table->date('slot_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('max_visitors')->default(50);
            $table->unsignedSmallInteger('booked_visitors')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['darshan_id', 'slot_date', 'start_time'], 'darshan_slots_unique');
            $table->index(['darshan_id', 'slot_date', 'is_active'], 'darshan_slots_lookup');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('darshan_slots');
        Schema::dropIfExists('darshan_images');
        Schema::dropIfExists('darshan');
    }
};

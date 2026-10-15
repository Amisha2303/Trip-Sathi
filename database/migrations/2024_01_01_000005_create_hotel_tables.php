<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description', 500)->nullable();
            $table->string('address');
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 12)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('rating', 3, 2)->default(0)->index();
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedTinyInteger('star_rating')->default(3)->index();
            $table->string('main_image')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->time('check_in_time')->default('14:00:00');
            $table->time('check_out_time')->default('11:00:00');
            $table->unsignedTinyInteger('min_nights')->default(1);
            $table->unsignedTinyInteger('max_nights')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('policies')->nullable();
            $table->decimal('base_price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'is_featured', 'rating'], 'hotels_listing_index');
            $table->index(['base_price', 'star_rating'], 'hotels_price_rating_index');
        });

        Schema::create('hotel_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['hotel_id', 'sort_order']);
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('room_type', 60)->default('double');
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(1);
            $table->unsignedTinyInteger('max_occupancy')->default(2);
            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('extra_bed_price', 10, 2)->default(0);
            $table->unsignedSmallInteger('total_rooms')->default(1);
            $table->string('bed_type', 60)->nullable();
            $table->unsignedSmallInteger('room_size_sqft')->nullable();
            $table->string('main_image')->nullable();
            $table->boolean('breakfast_included')->default(false);
            $table->boolean('is_refundable')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hotel_id', 'slug']);
            $table->index(['hotel_id', 'is_active']);
            $table->index(['is_active', 'price'], 'rooms_active_price_index');
        });

        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['room_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('hotel_images');
        Schema::dropIfExists('hotels');
    }
};

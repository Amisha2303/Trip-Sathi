<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('address');
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 12)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('rating', 3, 2)->default(0)->index();
            $table->unsignedInteger('review_count')->default(0);
            $table->string('cuisine', 150)->nullable()->index();
            $table->string('price_range', 20)->default('$$');
            $table->decimal('average_cost', 10, 2)->default(0);
            $table->string('main_image')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->time('opens_at')->default('12:00:00');
            $table->time('closes_at')->default('23:00:00');
            $table->unsignedTinyInteger('total_tables')->default(20);
            $table->unsignedSmallInteger('reservation_slot_minutes')->default(90);
            $table->boolean('online_reservation')->default(true);
            $table->text('offers')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'rating'], 'restaurants_listing_index');
        });

        Schema::create('restaurant_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['restaurant_id', 'sort_order']);
        });

        // A la carte / set menus shown on the restaurant page.
        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('section', 80)->default('Main Course');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_available')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_available', 'sort_order']);
            $table->softDeletes();
        });

        // Bookable culinary experiences (cooking classes, tasting walks, thali trails).
        Schema::create('food_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('main_image')->nullable();
            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(90);
            $table->unsignedTinyInteger('max_guests')->default(8);
            $table->unsignedTinyInteger('min_guests')->default(1);
            $table->string('cuisine', 150)->nullable();
            $table->text('includes')->nullable();       // JSON array
            $table->text('menu_highlights')->nullable(); // JSON array
            $table->text('safety_notes')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'is_featured'], 'food_listing_index');
        });

        Schema::create('food_experience_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_experience_id')->constrained('food_experiences')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['food_experience_id', 'sort_order']);
        });

        // Time slots for both table reservations and food experiences.
        Schema::create('restaurant_slots', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('slottable');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('capacity')->default(10);
            $table->unsignedSmallInteger('tables')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['slottable_type', 'slottable_id', 'is_active'], 'restaurant_slots_lookup');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_slots');
        Schema::dropIfExists('food_experience_images');
        Schema::dropIfExists('food_experiences');
        Schema::dropIfExists('restaurant_menus');
        Schema::dropIfExists('restaurant_images');
        Schema::dropIfExists('restaurants');
    }
};

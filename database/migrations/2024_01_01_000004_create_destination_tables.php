<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country', 100)->default('India');
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->text('best_time_to_visit')->nullable();
            $table->string('main_image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('language', 100)->nullable();
            $table->string('timezone')->nullable();
            $table->unsignedTinyInteger('best_months')->nullable(); // 1-12 bitmask hint
            $table->unsignedInteger('popularity')->default(0)->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_featured', 'popularity'], 'destinations_listing_index');
            $table->index(['country', 'state', 'city'], 'destinations_location_index');
        });

        Schema::create('destination_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['destination_id', 'sort_order']);
        });

        // Curated "don't miss" list rendered on the destination page.
        Schema::create('destination_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('entry_fee', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['destination_id', 'sort_order']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_places');
        Schema::dropIfExists('destination_images');
        Schema::dropIfExists('destinations');
    }
};

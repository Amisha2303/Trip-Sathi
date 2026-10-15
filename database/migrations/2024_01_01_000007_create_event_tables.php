<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            $table->string('venue');
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('organiser')->nullable();

            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->date('event_date')->index(); // denormalised for fast date filters
            $table->string('timezone')->nullable();

            $table->unsignedInteger('capacity')->default(100);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('reserved_count')->default(0);
            $table->decimal('base_price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->string('main_image')->nullable();
            $table->text('terms')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'event_date', 'starts_at'], 'events_schedule_index');
        });

        Schema::create('event_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'sort_order']);
        });

        Schema::create('event_ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('per_order_limit')->default(10);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'is_active']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ticket_types');
        Schema::dropIfExists('event_images');
        Schema::dropIfExists('events');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adventures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            $table->string('activity_type', 80)->nullable()->index(); // trekking|rafting|paragliding|scuba|camping|safari|zipline|watersports
            $table->text('safety_instructions')->nullable();
            $table->text('inclusions')->nullable();
            $table->text('equipment')->nullable();
            $table->text('cancellation_policy')->nullable();

            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(180);
            $table->string('difficulty', 20)->default('moderate')->index();
            $table->unsignedTinyInteger('min_age')->default(8);
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->unsignedTinyInteger('max_participants')->default(20);
            $table->unsignedTinyInteger('min_participants')->default(1);
            $table->decimal('height_min_cm')->nullable();
            $table->decimal('height_max_cm')->nullable();
            $table->decimal('weight_max_kg')->nullable();
            $table->text('meeting_point')->nullable();
            $table->string('main_image')->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['destination_id', 'is_active']);
            $table->index(['is_active', 'is_featured', 'price'], 'adventures_listing_index');
            $table->index(['activity_type', 'difficulty'], 'adventures_type_index');
        });

        Schema::create('adventure_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adventure_id')->constrained('adventures')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['adventure_id', 'sort_order']);
        });

        Schema::create('adventure_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adventure_id')->constrained('adventures')->cascadeOnDelete();
            $table->date('slot_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('capacity')->default(10);
            $table->unsignedSmallInteger('booked')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            // One row per date/time slot; the booking service relies on this
            // constraint together with a row lock to stop overbooking.
            $table->unique(['adventure_id', 'slot_date', 'start_time'], 'adventure_slots_unique');
            $table->index(['adventure_id', 'slot_date', 'is_active'], 'adventure_slots_lookup');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adventure_slots');
        Schema::dropIfExists('adventure_images');
        Schema::dropIfExists('adventures');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pooja Vidhi — ritual offerings bookable by slot. Structured like `darshan`
 * (which it is not a subtype of) so each product owns its capacity rules.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poojas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            $table->string('temple_name');
            $table->string('deity', 100)->nullable();
            $table->text('benefits')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->unsignedSmallInteger('max_participants')->default(50);
            $table->unsignedSmallInteger('min_participants')->default(1);
            $table->decimal('price', 10, 2)->default(0)->index();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('requirements')->nullable();   // what the devotee must bring
            $table->json('includes')->nullable();        // samagri list, prasad, gifts
            $table->text('process')->nullable();
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
            $table->index(['is_active', 'is_featured'], 'poojas_listing_index');
        });

        Schema::create('pooja_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pooja_id')->constrained('poojas')->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['pooja_id', 'sort_order']);
        });

        Schema::create('pooja_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pooja_id')->constrained('poojas')->cascadeOnDelete();
            $table->date('slot_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('max_participants')->default(50);
            $table->unsignedSmallInteger('booked_participants')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['pooja_id', 'slot_date', 'start_time'], 'pooja_slots_unique');
            $table->index(['pooja_id', 'slot_date', 'is_active'], 'pooja_slots_lookup');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pooja_slots');
        Schema::dropIfExists('pooja_images');
        Schema::dropIfExists('poojas');
    }
};

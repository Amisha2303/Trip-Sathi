<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shared catalogue building blocks.
 *
 * `amenities` is deliberately attached through one polymorphic pivot rather than
 * seven identical `*_amenities` tables: a hotel, a room and an adventure all
 * need tags, and duplicating the join three times would triple the places that
 * need fixing. Documented in README § Schema decisions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('parent', 'categories_parent');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 40)->index(); // destination|hotel|restaurant|event|adventure|darshan|pooja|trip
            $table->text('description')->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
            $table->softDeletes();
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 60)->nullable();
            $table->string('group', 60)->default('general')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'is_active']);
            $table->softDeletes();
        });

        Schema::create('amenity_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->morphs('ameniable');
            $table->timestamps();

            $table->unique(
                ['amenity_id', 'ameniable_id', 'ameniable_type'],
                'amenity_amenity_unique'
            );
            $table->index(['ameniable_type', 'ameniable_id'], 'amenity_amenity_subject_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_amenity');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('categories');
    }
};

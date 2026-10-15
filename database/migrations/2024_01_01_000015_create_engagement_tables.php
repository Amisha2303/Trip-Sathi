<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Engagement: wishlist (polymorphic), reviews verified against a completed
 * booking, review images, abuse reports and curated homepage testimonials.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('wishlistable');
            $table->string('type', 40)->index();
            $table->timestamps();

            // Toggle is idempotent: adding twice is an upsert, not a duplicate row.
            $table->unique(
                ['user_id', 'wishlistable_id', 'wishlistable_type'],
                'wishlists_unique'
            );
            $table->index(['user_id', 'type'], 'wishlists_user_type_index');
            $table->softDeletes();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->morphs('subject');
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('title')->nullable();
            $table->text('comment');
            $table->string('status', 20)->default('pending')->index();

            $table->boolean('is_verified')->default(false)->index();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('report_count')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // One review per booking — the real duplicate guard, enforced in the DB.
            $table->unique(['booking_id', 'subject_type', 'subject_id'], 'reviews_booking_subject_unique');
            $table->index(['subject_type', 'subject_id', 'status'], 'reviews_listing_index');
        });

        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['review_id']);
        });

        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 80);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();

            // One report per user per review.
            $table->unique(['review_id', 'user_id'], 'review_reports_unique');
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('review_id')->nullable()->constrained('reviews')->nullOnDelete();
            $table->string('author_name');
            $table->string('author_designation')->nullable();
            $table->string('author_avatar')->nullable();
            $table->string('location')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('review_reports');
        Schema::dropIfExists('review_images');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('wishlists');
    }
};

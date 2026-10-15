<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 20)->default('percentage'); // percentage|fixed
            $table->decimal('value', 10, 2);                    // % or currency amount
            $table->decimal('max_discount', 10, 2)->nullable(); // cap for percentage coupons
            $table->decimal('min_order_amount', 10, 2)->default(0);

            $table->json('applicable_types')->nullable();     // ["hotel","event"] or null = all
            $table->json('applicable_destinations')->nullable(); // ["ahmedabad"] slugs, or null = all

            $table->dateTime('starts_at')->nullable()->index();
            $table->dateTime('expires_at')->nullable()->index();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('per_user_limit')->default(1);

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'expires_at'], 'coupons_validity_index');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

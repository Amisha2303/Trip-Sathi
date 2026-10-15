<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * System-level tables: application settings (key/value, cached), admin activity
 * audit trail and the outbound email log used by the mail tester screen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 60)->default('general')->index();
            $table->string('key', 120)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string|boolean|integer|json|text|image
            $table->string('label')->nullable();
            $table->text('hint')->nullable();
            $table->boolean('is_public')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'sort_order']);
            $table->softDeletes();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40)->index();
            $table->string('module', 60)->nullable()->index();
            $table->string('description')->nullable();
            $table->nullableMorphs('subject');
            $table->string('method', 10)->nullable();
            $table->string('route', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['module', 'created_at']);
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable')->index();
            $table->nullableMorphs('notifiable');
            $table->string('subject')->nullable();
            $table->json('to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('status', 20)->default('queued')->index(); // queued|sent|failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
    }
};

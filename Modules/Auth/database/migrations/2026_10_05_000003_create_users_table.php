<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->nullable()->constrained('schools')->cascadeOnDelete();
            $table->string('username', 100);
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 50);
            $table->string('password', 255);
            $table->string('avatar_url', 500)->nullable();
            $table->enum('user_type', ['staff', 'parent', 'super_admin'])->default('parent');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending_activation'])->default('active');
            $table->json('metadata');
            $table->dateTime('last_login_at', 6)->nullable();
            $table->rememberToken();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['username', 'active_flag'], 'uq_users_username');
            $table->unique(['school_id', 'phone_number', 'active_flag'], 'uq_users_school_phone');
            $table->unique(['school_id', 'email', 'active_flag'], 'uq_users_school_email');
            $table->index('school_id', 'idx_users_school');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

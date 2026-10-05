<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('code', 50);
            $table->string('subdomain', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->enum('status', ['active', 'suspended', 'pending_setup'])->default('active');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            // Active flag for soft-delete safe unique constraints
            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['code', 'active_flag'], 'uq_schools_code');
            $table->unique(['subdomain', 'active_flag'], 'uq_schools_subdomain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};

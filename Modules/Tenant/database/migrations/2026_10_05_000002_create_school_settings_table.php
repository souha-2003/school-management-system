<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            
            // SaaS Subscription & Lease Period
            $table->string('subscription_plan', 50)->default('standard');
            $table->date('subscription_start_date');
            $table->date('subscription_end_date');
            
            // Theme & Branding
            $table->string('primary_color', 20)->default('#1E40AF');
            $table->string('secondary_color', 20)->default('#3B82F6');
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('light');
            $table->string('favicon_url', 500)->nullable();
            
            // Timetable & Working hours
            $table->string('timezone', 50)->default('Asia/Riyadh');
            $table->time('school_start_time')->default('07:30:00');
            $table->time('school_end_time')->default('14:00:00');
            $table->string('weekend_days', 50)->default('friday,saturday');
            $table->json('extra_config');
            
            $table->timestamps(6);
            
            $table->unique('school_id', 'uq_school_settings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};

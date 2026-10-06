<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('employee_number', 50)->nullable();
            $table->string('full_name', 255);
            $table->string('phone_number', 50);
            $table->string('email', 255)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('job_title', 100);
            $table->string('specialization', 100)->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active', 'on_leave', 'terminated'])->default('active');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['school_id', 'employee_number', 'active_flag'], 'uq_staff_emp_num');
            $table->unique('user_id', 'uq_staff_user');
            $table->index('school_id', 'idx_staff_school');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};

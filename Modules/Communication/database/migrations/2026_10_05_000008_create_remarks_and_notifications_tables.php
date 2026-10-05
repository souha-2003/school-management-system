<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remarks (Behavior & Academic notes)
        Schema::create('remarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignUuid('session_id')->nullable()->references('id')->on('class_sessions')->nullOnDelete();
            $table->enum('type', ['academic_positive', 'academic_concern', 'behavior_positive', 'behavior_warning', 'health_note', 'general'])->default('general');
            $table->enum('severity', ['low', 'medium', 'high', 'urgent'])->default('low');
            $table->enum('visibility', ['parents_and_staff', 'staff_only'])->default('parents_and_staff');
            $table->string('title', 255);
            $table->text('note_text');
            $table->boolean('acknowledged_by_parent')->default(false);
            $table->dateTime('parent_acknowledged_at', 6)->nullable();
            $table->foreignUuid('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['student_id', 'created_at'], 'idx_remarks_student');
            $table->index('teacher_id', 'idx_remarks_teacher');
        });

        // 2. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['attendance_alert', 'remark_alert', 'exam_result', 'fee_reminder', 'general_announcement'])->default('general_announcement');
            $table->enum('channel', ['in_app', 'push', 'sms', 'whatsapp', 'email'])->default('in_app');
            $table->string('title', 255);
            $table->text('body');
            $table->json('data');
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at', 6)->nullable();
            $table->dateTime('created_at', 6)->useCurrent();

            $table->index(['user_id', 'is_read', 'created_at'], 'idx_notifications_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('remarks');
    }
};

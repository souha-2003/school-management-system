<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Timetable Slots (Weekly Template)
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignUuid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('staff')->cascadeOnDelete();
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->integer('period_number');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['classroom_id', 'day_of_week'], 'idx_timetable_classroom');
            $table->index(['teacher_id', 'day_of_week'], 'idx_timetable_teacher');
        });

        // 2. Scheduled/Completed Sessions (Daily Lessons)
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('timetable_slot_id')->nullable()->constrained('timetable_slots')->nullOnDelete();
            $table->foreignUuid('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignUuid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('staff')->cascadeOnDelete();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('topic_title', 255)->nullable();
            $table->text('topic_description')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->json('metadata');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['classroom_id', 'session_date', 'teacher_id'], 'idx_sessions_lookup');
        });

        // 3. Attendance (Daily & Per-Session)
        Schema::create('attendance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('scope', ['daily', 'session'])->default('daily');
            $table->foreignUuid('session_id')->nullable()->references('id')->on('class_sessions')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'left_early'])->default('present');
            $table->dateTime('check_in_time', 6)->nullable();
            $table->dateTime('check_out_time', 6)->nullable();
            $table->integer('late_minutes')->default(0);
            $table->text('excuse_reason')->nullable();
            $table->foreignUuid('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['school_id', 'attendance_date', 'student_id'], 'idx_attendance_daily');
            $table->index(['session_id', 'student_id'], 'idx_attendance_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('class_sessions');
        Schema::dropIfExists('timetable_slots');
    }
};

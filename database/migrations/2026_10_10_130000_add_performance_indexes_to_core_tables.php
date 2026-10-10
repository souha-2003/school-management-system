<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Attendance: تسريع كشف وسجل حضور الطالب لفترة زمنية
        Schema::table('attendance', function (Blueprint $table) {
            $table->index(['student_id', 'attendance_date'], 'idx_attendance_student_date');
        });

        // 2. Students: تسريع جلب وعرض الطلاب النشطين بالمدرسة
        Schema::table('students', function (Blueprint $table) {
            $table->index(['school_id', 'is_archived'], 'idx_students_school_archived');
        });

        // 3. Class Sessions: تسريع استعلام جدول وحصص المعلم ليوم محدد
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->index(['teacher_id', 'session_date'], 'idx_sessions_teacher_date');
        });

        // 4. Users: تسريع تصفية المستخدمين حسب نوعهم وحالتهم بالمدرسة
        Schema::table('users', function (Blueprint $table) {
            $table->index(['school_id', 'user_type', 'status'], 'idx_users_school_type_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_school_type_status');
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sessions_teacher_date');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_school_archived');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_student_date');
        });
    }
};

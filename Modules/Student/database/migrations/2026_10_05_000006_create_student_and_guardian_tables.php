<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Students
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('admission_number', 100)->nullable();
            $table->string('first_name', 100);
            $table->string('second_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->string('national_id', 50)->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->json('metadata');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['school_id', 'admission_number', 'active_flag'], 'uq_students_admission_num');
            $table->unique(['school_id', 'national_id', 'active_flag'], 'uq_students_national_id');
            $table->index('school_id', 'idx_students_school');
        });

        // 2. Parents / Guardians
        Schema::create('parents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 255);
            $table->string('phone_number', 50);
            $table->string('email', 255)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('occupation', 150)->nullable();
            $table->text('address')->nullable();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['school_id', 'national_id', 'active_flag'], 'uq_parents_national_id');
            $table->unique('user_id', 'uq_parents_user');
            $table->index('school_id', 'idx_parents_school');
        });

        // 3. Student-Parent Pivot (Many-to-Many)
        Schema::create('student_parents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->enum('relationship_type', ['father', 'mother', 'brother', 'sister', 'legal_guardian', 'driver', 'other'])->default('father');
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_pickup')->default(true);
            $table->dateTime('created_at', 6)->useCurrent();

            $table->unique(['student_id', 'parent_id'], 'uq_student_parent');
            $table->index('student_id', 'idx_sp_student');
            $table->index('parent_id', 'idx_sp_parent');
        });

        // 4. Student Enrollments per Year & Classroom
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('classroom_id')->constrained('classrooms')->restrictOnDelete();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->integer('roll_number')->nullable();
            $table->enum('status', ['active', 'transferred', 'graduated', 'suspended', 'expelled'])->default('active');
            $table->date('enrolled_at')->useCurrent();
            $table->timestamps(6);

            $table->unique(['student_id', 'academic_year_id'], 'uq_student_year');
            $table->index(['classroom_id', 'academic_year_id'], 'idx_se_classroom_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('student_parents');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('students');
    }
};

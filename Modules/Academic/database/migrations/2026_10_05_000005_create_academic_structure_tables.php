<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['school_id', 'is_current'], 'idx_years_school_current');
        });

        // 2. Academic Terms
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index('academic_year_id', 'idx_terms_year');
        });

        // 3. Academic Levels (Grades)
        Schema::create('academic_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('level_order')->default(1);
            $table->string('stage', 100)->nullable();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index(['school_id', 'level_order'], 'idx_levels_school');
        });

        // 4. Classrooms (Sections)
        Schema::create('classrooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('academic_level_id')->constrained('academic_levels')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('room_number', 50)->nullable();
            $table->integer('capacity')->default(30);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->index('academic_level_id', 'idx_classrooms_level');
        });

        // 5. Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('credit_hours', 4, 2)->default(1.00);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['school_id', 'code', 'active_flag'], 'uq_subjects_code_school');
            $table->index('school_id', 'idx_subjects_school');
        });

        // 6. Distribution of subjects per level (Curriculum & Study Plan)
        Schema::create('academic_level_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('academic_level_id')->constrained('academic_levels')->cascadeOnDelete();
            $table->foreignUuid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('curriculum_name', 150)->nullable();
            $table->integer('weekly_periods')->default(1);
            $table->decimal('max_score', 5, 2)->default(100.00);
            $table->decimal('passing_score', 5, 2)->default(50.00);
            $table->boolean('is_elective')->default(false);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->tinyInteger('active_flag')->virtualAs('IF(deleted_at IS NULL, 1, NULL)')->nullable();

            $table->unique(['academic_level_id', 'subject_id', 'active_flag'], 'uq_level_subject');
            $table->index('school_id', 'idx_als_school');
            $table->index('academic_level_id', 'idx_als_level');
            $table->index('subject_id', 'idx_als_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_level_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('academic_levels');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_years');
    }
};

-- =============================================================================
-- School Management System - Database Schema (MySQL 8.0+ DDL)
-- Production-Ready, High-Scalability, Multi-Tenant Architecture
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Supports: UUIDs (CHAR(36)), Soft Deletes, RBAC, Timetables, Attendance & Audits
-- Features: Decoupled Auth Entity (users) from Domain Profiles (staff, parents)
--           Auto-generated Usernames (e.g. ROW-FAM-1029, ROW-STF-0104)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if needed (Clean setup order)
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS remarks;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS timetable_slots;
DROP TABLE IF EXISTS student_enrollments;
DROP TABLE IF EXISTS student_parents;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS parents;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS academic_level_subjects;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS classrooms;
DROP TABLE IF EXISTS academic_levels;
DROP TABLE IF EXISTS academic_terms;
DROP TABLE IF EXISTS academic_years;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS school_settings;
DROP TABLE IF EXISTS schools;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- 1. MULTI-TENANCY: SCHOOLS & SETTINGS
-- =============================================================================

CREATE TABLE schools (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    subdomain VARCHAR(100) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    logo_url VARCHAR(500) NULL,
    status ENUM('active', 'suspended', 'pending_setup') NOT NULL DEFAULT 'active',
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_schools_code (code, active_flag),
    UNIQUE KEY uq_schools_subdomain (subdomain, active_flag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- School Settings & SaaS Lease Period (One-to-One with schools)
CREATE TABLE school_settings (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    -- SaaS Subscription & Lease Period (فترة التأجير والاشتراك)
    subscription_plan VARCHAR(50) NOT NULL DEFAULT 'standard', -- e.g. 'trial', 'basic', 'standard', 'enterprise'
    subscription_start_date DATE NOT NULL,
    subscription_end_date DATE NOT NULL, -- تاريخ نهاية فترة التأجير / الاشتراك
    -- Theme & Branding (المظهر والسمات)
    primary_color VARCHAR(20) NOT NULL DEFAULT '#1E40AF',
    secondary_color VARCHAR(20) NOT NULL DEFAULT '#3B82F6',
    theme_mode ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'light',
    favicon_url VARCHAR(500) NULL,
    -- Academic & Timetable Config (مواعيد الدوام والتوقيت)
    timezone VARCHAR(50) NOT NULL DEFAULT 'Asia/Riyadh',
    school_start_time TIME NOT NULL DEFAULT '07:30:00',
    school_end_time TIME NOT NULL DEFAULT '14:00:00',
    weekend_days VARCHAR(50) NOT NULL DEFAULT 'friday,saturday',
    extra_config JSON NOT NULL, -- تخصيصات إضافية مرنة
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_settings_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    UNIQUE KEY uq_school_settings (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 2. AUTHENTICATION & ACCESS CONTROL (Pure Auth & RBAC)
-- =============================================================================

CREATE TABLE users (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    username VARCHAR(100) NOT NULL, -- اسم مستخدم مولد تلقائياً (e.g. ROW-FAM-1029, ROW-STF-0104)
    email VARCHAR(255) NULL,
    phone_number VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(500) NULL,
    user_type ENUM('staff', 'parent', 'super_admin') NOT NULL DEFAULT 'parent',
    status ENUM('active', 'inactive', 'suspended', 'pending_activation') NOT NULL DEFAULT 'active',
    metadata JSON NOT NULL,
    last_login_at DATETIME(6) NULL DEFAULT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_users_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    UNIQUE KEY uq_users_username (username, active_flag),
    UNIQUE KEY uq_users_school_phone (school_id, phone_number, active_flag),
    UNIQUE KEY uq_users_school_email (school_id, email, active_flag),
    KEY idx_users_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NULL, -- NULL for system-wide default roles
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_system_role TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_roles_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    KEY idx_roles_slug (slug),
    KEY idx_roles_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL, -- e.g. 'attendance', 'students', 'remarks'
    description TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_permissions_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    role_id CHAR(36) NOT NULL,
    permission_id CHAR(36) NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
    user_id CHAR(36) NOT NULL,
    role_id CHAR(36) NOT NULL,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 3. DOMAIN PROFILES (Staff & Parents - Independent from Auth Account)
-- =============================================================================

-- Staff & Teachers Profile (user_id is NULL until app/portal account is activated)
CREATE TABLE staff (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    user_id CHAR(36) NULL, -- NULL طالما لم يتم تفعيل الحساب
    employee_number VARCHAR(50) NULL, -- الرقم الوظيفي
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    email VARCHAR(255) NULL,
    national_id VARCHAR(50) NULL,
    job_title VARCHAR(100) NOT NULL, -- e.g. 'Teacher', 'Principal', 'Supervisor', 'Accountant'
    specialization VARCHAR(100) NULL, -- تخصص المعلم (رياضيات، علوم، لغة عربية)
    hire_date DATE NULL,
    status ENUM('active', 'on_leave', 'terminated') NOT NULL DEFAULT 'active',
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_staff_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_staff_emp_num (school_id, employee_number, active_flag),
    UNIQUE KEY uq_staff_user (user_id),
    KEY idx_staff_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Parents & Guardians Profile (user_id is NULL until mobile app is activated)
CREATE TABLE parents (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    user_id CHAR(36) NULL, -- NULL طالما لم يقم ولي الأمر بتفعيل التطبيق
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    email VARCHAR(255) NULL,
    national_id VARCHAR(50) NULL,
    occupation VARCHAR(150) NULL, -- وظيفة ولي الأمر
    address TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_parents_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_parents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_parents_national_id (school_id, national_id, active_flag),
    UNIQUE KEY uq_parents_user (user_id),
    KEY idx_parents_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 4. ACADEMIC STRUCTURE (Years, Terms, Levels, Classrooms, Subjects)
-- =============================================================================

CREATE TABLE academic_years (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL, -- e.g. '2026-2027'
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_academic_years_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    KEY idx_years_school_current (school_id, is_current)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE academic_terms (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    academic_year_id CHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL, -- e.g. 'Term 1', 'Semester 1'
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_terms_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    KEY idx_terms_year (academic_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE academic_levels (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL, -- e.g. 'Level 10', 'الصف العاشر / الأول الثانوي'
    level_order INT NOT NULL DEFAULT 1,
    stage VARCHAR(100) NULL, -- 'Primary', 'Middle', 'High School'
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_levels_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    KEY idx_levels_school (school_id, level_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE classrooms (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    academic_level_id CHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL, -- e.g. 'Section A', '10-1'
    room_number VARCHAR(50) NULL,
    capacity INT DEFAULT 30,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_classrooms_level FOREIGN KEY (academic_level_id) REFERENCES academic_levels(id) ON DELETE CASCADE,
    KEY idx_classrooms_level (academic_level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subjects (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    credit_hours DECIMAL(4,2) DEFAULT 1.00,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_subjects_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    UNIQUE KEY uq_subjects_code_school (school_id, code, active_flag),
    KEY idx_subjects_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Distribution of subjects per academic level (Curriculum, Weekly Load & Passing Scores)
CREATE TABLE academic_level_subjects (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    academic_level_id CHAR(36) NOT NULL,
    subject_id CHAR(36) NOT NULL,
    curriculum_name VARCHAR(150) NULL, -- اسم المنهاج أو الكتاب المعتمد للصف
    weekly_periods INT NOT NULL DEFAULT 1, -- عدد الحصص الأسبوعية للمادة في هذا الصف
    max_score DECIMAL(5,2) NULL DEFAULT 100.00, -- الدرجة العظمى
    passing_score DECIMAL(5,2) NULL DEFAULT 50.00, -- درجة النجاح
    is_elective TINYINT(1) NOT NULL DEFAULT 0, -- 0 = إجباري، 1 = اختياري
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_als_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_als_level FOREIGN KEY (academic_level_id) REFERENCES academic_levels(id) ON DELETE CASCADE,
    CONSTRAINT fk_als_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY uq_level_subject (academic_level_id, subject_id, active_flag),
    KEY idx_als_school (school_id),
    KEY idx_als_level (academic_level_id),
    KEY idx_als_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 5. STUDENTS, GUARDIANS & ENROLLMENTS
-- =============================================================================

CREATE TABLE students (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    admission_number VARCHAR(100) NULL,
    first_name VARCHAR(100) NOT NULL,
    second_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    birth_date DATE NOT NULL,
    national_id VARCHAR(50) NULL,
    photo_url VARCHAR(500) NULL,
    metadata JSON NOT NULL, -- Health notes, dietary alerts, allergies, emergency contacts
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    active_flag TINYINT(1) GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL,
    PRIMARY KEY (id),
    CONSTRAINT fk_students_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    UNIQUE KEY uq_students_admission_num (school_id, admission_number, active_flag),
    UNIQUE KEY uq_students_national_id (school_id, national_id, active_flag),
    KEY idx_students_school (school_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot Table: Linking Students to Guardian Profiles (Many-to-Many)
CREATE TABLE student_parents (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    student_id CHAR(36) NOT NULL,
    parent_id CHAR(36) NOT NULL,
    relationship_type ENUM('father', 'mother', 'brother', 'sister', 'legal_guardian', 'driver', 'other') NOT NULL DEFAULT 'father',
    is_primary_contact TINYINT(1) NOT NULL DEFAULT 0,
    can_pickup TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_sp_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_sp_parent FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE,
    UNIQUE KEY uq_student_parent (student_id, parent_id),
    KEY idx_sp_student (student_id),
    KEY idx_sp_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Academic Enrollment per Year & Section
CREATE TABLE student_enrollments (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    student_id CHAR(36) NOT NULL,
    classroom_id CHAR(36) NOT NULL,
    academic_year_id CHAR(36) NOT NULL,
    roll_number INT NULL,
    status ENUM('active', 'transferred', 'graduated', 'suspended', 'expelled') NOT NULL DEFAULT 'active',
    enrolled_at DATE NOT NULL DEFAULT (CURRENT_DATE),
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_se_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_se_classroom FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE RESTRICT,
    CONSTRAINT fk_se_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_student_year (student_id, academic_year_id),
    KEY idx_se_classroom_year (classroom_id, academic_year_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 6. TIMETABLE & SESSIONS (Scheduling & Lesson Logs)
-- =============================================================================

CREATE TABLE timetable_slots (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    classroom_id CHAR(36) NOT NULL,
    subject_id CHAR(36) NOT NULL,
    teacher_id CHAR(36) NOT NULL, -- references staff.id
    day_of_week ENUM('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') NOT NULL,
    period_number INT NOT NULL, -- 1st period, 2nd period...
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_ts_classroom FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    CONSTRAINT fk_ts_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT fk_ts_teacher FOREIGN KEY (teacher_id) REFERENCES staff(id) ON DELETE CASCADE,
    KEY idx_timetable_classroom (classroom_id, day_of_week),
    KEY idx_timetable_teacher (teacher_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    timetable_slot_id CHAR(36) NULL,
    classroom_id CHAR(36) NOT NULL,
    subject_id CHAR(36) NOT NULL,
    teacher_id CHAR(36) NOT NULL, -- references staff.id
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    topic_title VARCHAR(255) NULL,
    topic_description TEXT NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    metadata JSON NOT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_sess_slot FOREIGN KEY (timetable_slot_id) REFERENCES timetable_slots(id) ON DELETE SET NULL,
    CONSTRAINT fk_sess_classroom FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    CONSTRAINT fk_sess_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT fk_sess_teacher FOREIGN KEY (teacher_id) REFERENCES staff(id) ON DELETE CASCADE,
    KEY idx_sessions_lookup (classroom_id, session_date, teacher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 7. ATTENDANCE & DISCIPLINE
-- =============================================================================

CREATE TABLE attendance (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    attendance_date DATE NOT NULL,
    scope ENUM('daily', 'session') NOT NULL DEFAULT 'daily',
    session_id CHAR(36) NULL,
    status ENUM('present', 'absent', 'late', 'excused', 'left_early') NOT NULL DEFAULT 'present',
    check_in_time DATETIME(6) NULL,
    check_out_time DATETIME(6) NULL,
    late_minutes INT DEFAULT 0,
    excuse_reason TEXT NULL,
    recorded_by CHAR(36) NULL, -- references users.id (the authenticated account that took attendance)
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_att_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_session FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_recorded_by FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_attendance_daily (school_id, attendance_date, student_id),
    KEY idx_attendance_session (session_id, student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 8. REMARKS & COMMUNICATION (Notes, Feedback, Behavior)
-- =============================================================================

CREATE TABLE remarks (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    teacher_id CHAR(36) NOT NULL, -- references staff.id
    session_id CHAR(36) NULL,
    type ENUM('academic_positive', 'academic_concern', 'behavior_positive', 'behavior_warning', 'health_note', 'general') NOT NULL DEFAULT 'general',
    severity ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'low',
    visibility ENUM('parents_and_staff', 'staff_only') NOT NULL DEFAULT 'parents_and_staff',
    title VARCHAR(255) NOT NULL,
    note_text TEXT NOT NULL,
    acknowledged_by_parent TINYINT(1) NOT NULL DEFAULT 0,
    parent_acknowledged_at DATETIME(6) NULL,
    parent_id CHAR(36) NULL, -- references parents.id (the parent who acknowledged)
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    deleted_at DATETIME(6) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_rem_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_rem_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_rem_teacher FOREIGN KEY (teacher_id) REFERENCES staff(id) ON DELETE CASCADE,
    CONSTRAINT fk_rem_session FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE SET NULL,
    CONSTRAINT fk_rem_parent FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE SET NULL,
    KEY idx_remarks_student (student_id, created_at),
    KEY idx_remarks_teacher (teacher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- 9. NOTIFICATIONS & AUDIT LOGS
-- =============================================================================

CREATE TABLE notifications (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    type ENUM('attendance_alert', 'remark_alert', 'exam_result', 'fee_reminder', 'general_announcement') NOT NULL DEFAULT 'general_announcement',
    channel ENUM('in_app', 'push', 'sms', 'whatsapp', 'email') NOT NULL DEFAULT 'in_app',
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    data JSON NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME(6) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_notifications_user (user_id, is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    school_id CHAR(36) NULL,
    user_id CHAR(36) NULL,
    action VARCHAR(150) NOT NULL, -- e.g. 'attendance.updated', 'student.deleted'
    entity_name VARCHAR(100) NOT NULL, -- e.g. 'attendance', 'student'
    entity_id CHAR(36) NOT NULL,
    old_data JSON NULL,
    new_data JSON NULL,
    ip_address VARCHAR(50) NULL,
    user_agent TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    CONSTRAINT fk_audit_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_audit_logs_school (school_id, created_at),
    KEY idx_audit_logs_entity (entity_name, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

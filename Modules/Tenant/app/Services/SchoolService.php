<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
use Modules\Tenant\Models\School;
use Modules\Tenant\Models\SchoolSetting;

class SchoolService
{
    /**
     * Retrieve paginated list of schools with filters.
     */
    public function listSchools(array $filters = [], $perPage = 15)
    {
        $query = School::query()->with('settings');

        // Search by keyword (name, code, subdomain, email, phone)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    /**
     * Get school by its UUID or fail.
     */
    public function getSchoolById($id)
    {
        return School::with('settings')->findOrFail($id);
    }

    /**
     * Create a new school alongside its initial settings inside a transaction.
     */
    public function createSchool(array $data)
    {
        return DB::transaction(function () use ($data) {
            $settingsData = $data['settings'] ?? [];
            unset($data['settings']);

            $managerData = $data['manager'] ?? null;
            unset($data['manager']);

            // 1. Create School
            $school = School::create($data);

            // 2. Prepare Settings with default fallback values if omitted
            $defaultSettings = [
                'subscription_plan' => $settingsData['subscription_plan'] ?? 'standard',
                'subscription_start_date' => $settingsData['subscription_start_date'] ?? now()->toDateString(),
                'subscription_end_date' => $settingsData['subscription_end_date'] ?? now()->addYear()->toDateString(),
                'primary_color' => $settingsData['primary_color'] ?? '#1E40AF',
                'secondary_color' => $settingsData['secondary_color'] ?? '#3B82F6',
                'theme_mode' => $settingsData['theme_mode'] ?? 'light',
                'favicon_url' => $settingsData['favicon_url'] ?? null,
                'timezone' => $settingsData['timezone'] ?? 'Asia/Riyadh',
                'school_start_time' => $settingsData['school_start_time'] ?? '07:30:00',
                'school_end_time' => $settingsData['school_end_time'] ?? '14:00:00',
                'weekend_days' => $settingsData['weekend_days'] ?? 'friday,saturday',
                'extra_config' => $settingsData['extra_config'] ?? [],
            ];

            $school->settings()->create($defaultSettings);

            // 3. Create initial School Manager account if provided
            if (!empty($managerData)) {
                $rawPassword = $managerData['password'] ?? 'School@' . rand(1000, 9999);
                $userId = (string) \Illuminate\Support\Str::uuid();
                $staffId = (string) \Illuminate\Support\Str::uuid();
                $username = strtoupper($school->code) . '-ADM-' . rand(1000, 9999);

                DB::table('users')->insert([
                    'id' => $userId,
                    'school_id' => $school->id,
                    'username' => $username,
                    'email' => $managerData['email'] ?? null,
                    'phone_number' => $managerData['phone_number'],
                    'password' => \Illuminate\Support\Facades\Hash::make($rawPassword),
                    'user_type' => 'staff',
                    'status' => 'active',
                    'metadata' => json_encode(['initial_role' => 'school_admin']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('staff')->insert([
                    'id' => $staffId,
                    'school_id' => $school->id,
                    'user_id' => $userId,
                    'employee_number' => 'EMP-0001',
                    'full_name' => $managerData['full_name'],
                    'phone_number' => $managerData['phone_number'],
                    'email' => $managerData['email'] ?? null,
                    'job_title' => 'مدير المدرسة',
                    'status' => 'active',
                    'is_archived' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $school->setAttribute('manager_credentials', [
                    'username' => $username,
                    'initial_password' => $rawPassword,
                    'full_name' => $managerData['full_name'],
                ]);
            }

            return $school->load('settings');
        });
    }

    /**
     * Update school details and optionally its settings.
     */
    public function updateSchool($school, array $data)
    {
        return DB::transaction(function () use ($school, $data) {
            if (isset($data['settings']) && is_array($data['settings'])) {
                $this->updateSchoolSettings($school, $data['settings']);
                unset($data['settings']);
            }

            if (!empty($data)) {
                $school->update($data);
            }

            return $school->fresh(['settings']);
        });
    }

    /**
     * Update or create settings for a specific school.
     */
    public function updateSchoolSettings($school, array $settingsData)
    {
        return $school->settings()->updateOrCreate(
            ['school_id' => $school->id],
            $settingsData
        );
    }

    /**
     * Change school operational status.
     */
    public function changeStatus($school, $status)
    {
        $school->update(['status' => $status]);

        return $school;
    }

    /**
     * Soft delete school.
     */
    public function deleteSchool($school)
    {
        return $school->delete();
    }
}

<?php

namespace Modules\Tenant\Services;

use Illuminate\Validation\ValidationException;
use Modules\Tenant\Models\School;

class SubscriptionService
{
    /**
     * Check if a school's subscription has expired.
     */
    public function isExpired(School $school): bool
    {
        $endDate = $school->settings?->subscription_end_date;

        if (!$endDate) {
            return false;
        }

        return now()->startOfDay()->gt($endDate);
    }

    /**
     * Get plan configuration and quota details for the school.
     */
    public function getPlanConfig(School $school): array
    {
        $planKey = strtolower($school->settings?->subscription_plan ?? 'standard');
        $allPlans = config('plans', []);

        return $allPlans[$planKey] ?? $allPlans['standard'] ?? [
            'name' => 'الباقة القياسية',
            'max_students' => 500,
            'max_staff' => 35,
            'features' => ['*'],
        ];
    }

    /**
     * Verify that the school can add a new staff member within its plan limits.
     */
    public function assertCanAddStaff(School $school): void
    {
        if ($this->isExpired($school)) {
            throw ValidationException::withMessages([
                'subscription' => ['انتهت فترة اشتراك هذه المدرسة، يرجى تجديد الاشتراك لمتابعة العمليات.'],
            ]);
        }

        $plan = $this->getPlanConfig($school);
        $maxStaff = $plan['max_staff'];

        if ($maxStaff !== -1) {
            $currentStaffCount = $school->staff()->where('status', 'active')->count();
            if ($currentStaffCount >= $maxStaff) {
                throw ValidationException::withMessages([
                    'subscription' => ["بلغت المدرسة الحد الأقصى المسموح به لعدد الكادر في ({$plan['name']}) وهو ({$maxStaff}) موظف، يرجى ترقية الباقة."],
                ]);
            }
        }
    }
}

<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tenant\Models\SchoolSetting;

class UpdateSchoolSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = $this->route('school');
        $existingSetting = $schoolId ? SchoolSetting::where('school_id', $schoolId)->first() : null;

        return [
            'subscription_plan' => ['sometimes', 'required', 'string', 'max:50'],
            'subscription_start_date' => [
                'sometimes',
                'required',
                'date',
                function ($attribute, $value, $fail) use ($existingSetting) {
                    $endDate = $this->input('subscription_end_date')
                        ?? ($existingSetting?->subscription_end_date instanceof \DateTimeInterface
                            ? $existingSetting->subscription_end_date->format('Y-m-d')
                            : $existingSetting?->subscription_end_date);

                    if ($endDate && strtotime($value) > strtotime($endDate)) {
                        $fail('تاريخ بداية الاشتراك لا يمكن أن يكون بعد تاريخ نهاية الاشتراك.');
                    }
                },
            ],
            'subscription_end_date' => [
                'sometimes',
                'required',
                'date',
                function ($attribute, $value, $fail) use ($existingSetting) {
                    $startDate = $this->input('subscription_start_date')
                        ?? ($existingSetting?->subscription_start_date instanceof \DateTimeInterface
                            ? $existingSetting->subscription_start_date->format('Y-m-d')
                            : $existingSetting?->subscription_start_date);

                    if ($startDate && strtotime($value) < strtotime($startDate)) {
                        $fail('تاريخ نهاية الاشتراك يجب أن يكون مساوياً أو بعد تاريخ بداية الاشتراك.');
                    }
                },
            ],
            'primary_color' => ['sometimes', 'required', 'string', 'max:20'],
            'secondary_color' => ['sometimes', 'required', 'string', 'max:20'],
            'theme_mode' => ['sometimes', 'required', 'string', Rule::in(['light', 'dark', 'system'])],
            'favicon_url' => ['nullable', 'string', 'max:500'],
            'timezone' => ['sometimes', 'required', 'string', 'max:50'],
            'school_start_time' => ['sometimes', 'required', 'date_format:H:i,H:i:s'],
            'school_end_time' => ['sometimes', 'required', 'date_format:H:i,H:i:s'],
            'weekend_days' => ['sometimes', 'required', 'string', 'max:50'],
            'extra_config' => ['nullable', 'array'],
        ];
    }
}

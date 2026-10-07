<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        return [
            'subscription_plan' => ['sometimes', 'required', 'string', 'max:50'],
            'subscription_start_date' => ['sometimes', 'required', 'date'],
            'subscription_end_date' => [
                'sometimes',
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $startDate = $this->input('subscription_start_date');
                    if ($startDate && strtotime($value) < strtotime($startDate)) {
                        $fail(__('validation.after_or_equal', ['attribute' => $attribute, 'date' => 'subscription_start_date']));
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

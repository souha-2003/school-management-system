<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reserved subdomains prohibited for schools.
     */
    public const RESERVED_SUBDOMAINS = [
        'admin', 'administrator', 'api', 'app', 'auth', 'dashboard', 'dev', 'developer',
        'mail', 'portal', 'root', 'schools', 'staging', 'support', 'system', 'tenant', 'test', 'www'
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('schools', 'code')->whereNull('deleted_at'),
            ],
            'subdomain' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/i',
                Rule::notIn(self::RESERVED_SUBDOMAINS),
                Rule::unique('schools', 'subdomain')->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', Rule::in(['active', 'suspended', 'pending_setup'])],

            // Optional initial settings configuration
            'settings' => ['nullable', 'array'],
            'settings.subscription_plan' => ['nullable', 'string', 'max:50'],
            'settings.subscription_start_date' => ['nullable', 'date'],
            'settings.subscription_end_date' => ['nullable', 'date', 'after_or_equal:settings.subscription_start_date'],
            'settings.primary_color' => ['nullable', 'string', 'max:20'],
            'settings.secondary_color' => ['nullable', 'string', 'max:20'],
            'settings.theme_mode' => ['nullable', 'string', Rule::in(['light', 'dark', 'system'])],
            'settings.favicon_url' => ['nullable', 'string', 'max:500'],
            'settings.timezone' => ['nullable', 'string', 'max:50'],
            'settings.school_start_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'settings.school_end_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'settings.weekend_days' => ['nullable', 'string', 'max:50'],
            'settings.extra_config' => ['nullable', 'array'],

            // Optional initial school manager account
            'manager' => ['nullable', 'array'],
            'manager.full_name' => ['required_with:manager', 'string', 'max:255'],
            'manager.email' => ['nullable', 'email', 'max:255'],
            'manager.phone_number' => ['required_with:manager', 'string', 'max:50'],
            'manager.password' => ['nullable', 'string', 'min:8'],
        ];
    }
}

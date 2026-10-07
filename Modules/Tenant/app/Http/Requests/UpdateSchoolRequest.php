<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
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
        $school = $this->route('school');
        $schoolId = is_object($school) ? $school->id : $school;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('schools', 'code')->whereNull('deleted_at')->ignore($schoolId),
            ],
            'subdomain' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('schools', 'subdomain')->whereNull('deleted_at')->ignore($schoolId),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', 'string', Rule::in(['active', 'suspended', 'pending_setup'])],
        ];
    }
}

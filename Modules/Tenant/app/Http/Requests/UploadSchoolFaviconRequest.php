<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadSchoolFaviconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'favicon' => ['required', 'image', 'mimes:jpeg,png,ico,svg', 'max:1024'],
        ];
    }
}

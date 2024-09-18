<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class SearchSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'website' => 'nullable|url',
            'founding_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'student_capacity' => 'nullable|integer|min:1',
        ];
    }
}

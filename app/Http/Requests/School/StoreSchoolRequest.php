<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'address' => 'required|string',
            'description' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'founding_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'student_capacity' => 'nullable|integer|min:1',
            'role' => 'required|string|in:school', // Ensure user role is 'school'

        ];
    }
}
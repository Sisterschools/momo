<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validation for Student-specific fields
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',

            // Validation for the User model
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            // Validation for the associated school
            'school_ids' => 'required|array',
            'school_ids.*' => 'exists:schools,id',

            // Role validation (ensure it is always 'student')
            'role' => 'required|string|in:student',
        ];
    }

    public function messages(): array
    {
        return [
            'school_ids.required' => 'At least one school must be selected.',
            'school_ids.*.exists' => 'Selected school does not exist.',
        ];
    }
}

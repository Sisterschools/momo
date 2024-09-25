<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validation for Teacher-specific fields
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string',

            // Validation for the User model
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            // Validation for the associated school
            'school_ids' => 'required|array',
            'school_ids.*' => 'exists:schools,id',

            // Role validation (ensure it is always 'teacher')
            'role' => 'required|string|in:teacher',
        ];
    }

    public function messages(): array
    {
        return [
            'school_ids.required' => 'At least one school must be selected.',
            'school_ids.*.exists' => 'Selected school does not exist.',
        ];
    }

    // Handle validation errors
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}

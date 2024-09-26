<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'address' => 'required|string',
            'description' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'founding_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'student_capacity' => 'nullable|integer|min:1',

            // Validation for the User model
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            // Role validation (ensure it is always 'school')
            'role' => 'required|string|in:school',

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
<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validation for Teacher-specific fields
            'name' => 'sometimes|required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'phone_number' => 'sometimes|required|string|max:15|unique:teachers',
            'bio' => 'nullable|string|max:1000',
            
        ];
    }
}

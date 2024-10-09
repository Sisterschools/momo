<?php
namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class AttachTeachersToSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id', // Ensure each ID exists in the teachers table
        ];
    }
}

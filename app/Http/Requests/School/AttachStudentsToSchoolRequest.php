<?php
namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class AttachStudentsToSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id', // Ensure each ID exists in the students table
        ];
    }
}

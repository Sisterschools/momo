<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Student;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttachStudentsToProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules(): array
    {
        return [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => 'You must provide at least one student ID',
            'student_ids.array' => 'Student IDs must be provided as an array',
            'student_ids.*.exists' => 'One or more student IDs are invalid'
        ];
    }


    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $project = $this->route('project');

            // Get the school IDs linked to the project
            $school1 = $project->school1;
            $school2 = $project->school2;

            // Check if each teacher being attached belongs to one of the project's schools
            foreach ($this->student_ids as $studentId) {
                $student = Student::find($studentId);

                // Ensure teacher belongs to either school1 or school2
                if (
                    !$student->schools()->where('schools.id', $school1->id)->exists() &&
                    !$student->schools()->where('schools.id', $school2->id)->exists()
                ) {
                    $validator->errors()->add('student_ids', "Student with ID {$studentId} does not belong to any school linked to this project.");
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}

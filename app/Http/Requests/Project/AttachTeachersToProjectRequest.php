<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Teacher;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttachTeachersToProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic if needed
    }

    public function rules(): array
    {
        return [
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:teachers,id'
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_ids.required' => 'You must provide at least one teacher ID',
            'teacher_ids.array' => 'teacher IDs must be provided as an array',
            'teacher_ids.*.exists' => 'One or more teacher IDs are invalid'
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
            foreach ($this->teacher_ids as $teacherId) {
                $teacher = Teacher::find($teacherId);

                // Ensure teacher belongs to either school1 or school2
                if (
                    !$teacher->schools()->where('schools.id', $school1->id)->exists() &&
                    !$teacher->schools()->where('schools.id', $school2->id)->exists()
                ) {
                    $validator->errors()->add('teacher_ids', "Teacher with ID {$teacherId} does not belong to any school linked to this project.");
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

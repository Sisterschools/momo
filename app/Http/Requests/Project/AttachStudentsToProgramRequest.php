<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttachStudentsToProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
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

    // public function withValidator(Validator $validator): void
    // {
    //     // check if the program is part of the project
    //     $validator->after(function ($validator) {
    //         $project = $this->route('project');
    //         $program = $this->route('program');

    //         if (!$project->programs()->where('programs.id', $program->id)->exists()) {
    //             $validator->errors()->add('program', 'Program is not attached to this project');
    //         }

    //         // check if the student is part of the project
    //         // Get the student IDs that belong to the project
    //         $projectStudentIds = $project->students()->pluck('id')->toArray();

    //         // Check if all students being attached belong to the project
    //         foreach ($this->student_ids as $studentId) {
    //             if (!in_array($studentId, $projectStudentIds)) {
    //                 $validator->errors()->add('student_ids', "Student with ID {$studentId} does not belong to this project.");
    //             }
    //         }
    //     });
    // }
}
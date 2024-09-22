<?php
namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AttachProgramProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            // Add any additional fields you want to validate when attaching a program
        ];
    }
}
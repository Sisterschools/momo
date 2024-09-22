<?php
namespace App\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class SearchProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic.
    }

    public function rules(): array
    {
        return [
            'search' => 'required|string|max:255', // Ensure a valid search term is provided.
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => 'Please enter a search term.',
            'search.string' => 'The search term must be a valid string.',
            'search.max' => 'The search term cannot exceed 255 characters.',
        ];
    }
}

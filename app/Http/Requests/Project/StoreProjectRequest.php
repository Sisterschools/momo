<?php
namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'school_id_1' => 'required|exists:schools,id',
            'school_id_2' => 'required|exists:schools,id|different:school_id_1',
        ];
    }
}
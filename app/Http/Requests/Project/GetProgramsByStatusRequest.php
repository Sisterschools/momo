<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class GetProgramsByStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change as necessary for your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:not ready,ready,archived',
        ];
    }

    /**
     * Validate the route parameters.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->route('status'),
        ]);
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of the following: not ready, ready, or archived.',
        ];
    }
}

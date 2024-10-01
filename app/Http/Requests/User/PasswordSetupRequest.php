<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PasswordSetupRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // You can adjust this based on your authorization needs
    }

    public function rules()
    {
        return [
            'token' => 'required',
            'password' => 'required|string|confirmed|min:8',
            'email' => 'required|email',
        ];
    }
}

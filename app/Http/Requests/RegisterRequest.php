<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => "required|min:3",
            'age'  => "required|numeric|min:1",
            'email' => "required|email|unique:users,email",
            'role' => "required|in:Doctor,Patient",
            'password' => "required|min:4|max:20",
            'password_confirmation' => "required|same:password",
            'role' => "required|in:Doctor,Patient",
        ];
    }

    public function validated($key = null, $default = null)
    {
        return array_merge($this->validator->validated(), [
            'password' => Hash::make(request('password'))
        ]);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRegisterRequest extends FormRequest
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
            'name' => ['required', 'regex:/^[a-zA-Z\\s]+$/u', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'contact_details' => ['required', 'string'],
            'specialties' => ['required', 'string'],
            'availability' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.regex' => 'Name must only contain alphabetical characters.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name can not be more than 255 characters long.',

            'age.required' => 'Age is required.',
            'age.integer' => 'Age must be an integer.',
            'age.min' => 'Age must be a grater than 0.',

            'email.required' => 'Email is required.',
            'email.string' => 'Email must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists.',

            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least eight character long.',

            'password_confirmation.required' => 'Confirm Password is required.',
            'password_confirmation.string' => 'Confirm Password must be a string.',
            'password_confirmation.same' => 'Confirm Password does not match with Password.',
        ];
    }
}

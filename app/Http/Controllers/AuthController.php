<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function checkLogin(Request $request)
    {
        $formData = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        // Validate the form data
        $validator = Validator::make(
            $formData,
            [
                'email' =>'required|string|email|exists:users',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'Email is required.',
                'email.string' => 'Email must be a string.',
                'email.email' => 'Email must be a valid email address.',
                'email.exists' => 'User with this email does not exist.',

                'password.required' => 'Password is required.',
                'password.string' => 'Password must be a string.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        }

        if (Auth::attempt($validator->validated())) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->withInput($request->all())->withErrors(['password' => 'Email or password is incorrect.']);
        }
    }
}

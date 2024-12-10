<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'age' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
                'role' => 'required|in:Admin,Doctor,Patient',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 400);
            }

            $user = User::create([
                'name' => $request->name,
                'age' => $request->age,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $responseData = collect($user)->only(['id', 'name', 'age', 'email', 'role']);

            return response()->json(
                [
                    'status' => 1,
                    'message' => 'User registered successfully',
                    'data' => $responseData,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => 'An error occurred during registration.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => $validator->errors()->first(),
                    ],
                    400,
                );
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => 'Invalid credentials.',
                    ],
                    401,
                );
            }

            $user = Auth::user();
            $token = auth()->login($user);

            return response()->json([
                'status' => 1,
                'message' => 'Login successful.',
                'token' => $token,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => 'An error occurred during login.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}

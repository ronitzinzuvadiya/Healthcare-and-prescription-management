<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\Response;

use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd($request);
        try {

            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'age'       => 'required|integer',
                'email'     => 'required|email|max:255|unique:users',
                'password'  => 'required|min:8',
                'role'      => 'required|in:Admin,Doctor,Patient',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
            }

            $form_data['name'] = $request->name;
            $form_data['age'] = $request->age;
            $form_data['email'] = $request->email;
            $form_data['password'] = Hash::make($request->password);
            $form_data['role'] = $request->role;

            // dd($form_data);

            $token = '';
            if($request->is_verified == '1') {
                $form_data['email_verified_at'] = Carbon::now();
                $response = User::create($form_data);
                $token = $response->createToken('Laravel_app')->plainTextToken;
                return response()->json(['status' => 1, 'is_user_deatils' => 0, 'message' => "Your account has been created successfully.",'token' => $token,'data' => $response]);
            } else {
                $emailReponse['otp'] = rand(1000, 9999);

                
            }


            $user = User::create([
                'name' => $validatedData['name'],
                'age' => $validatedData['age'],
                'role' => $validatedData['role'],
                'email' => $validatedData['email'],
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
            'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => 'Something went wrong please try again!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

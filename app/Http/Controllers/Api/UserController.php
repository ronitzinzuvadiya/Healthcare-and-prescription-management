<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    # Register user
    public function register(Request $request)
    {
        $formData = [
            'name' => $request->input('name'),
            'age' => $request->input('age'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'role' => $request->input('role'),
        ];

        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => 'Email already exists.'
                    ],
                    400
                );
            }

            # Validate the form data
            $validator = Validator::make(
                $formData,
                [
                    'name' => 'required|string|min:2|max:255',
                    'age' => 'required|min:1',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required|in:Doctor,Patient',
                ],
                [
                    'name.required' => 'Name is required.',
                    'name.string' => 'Name must be a string.',
                    'name.min' => 'Name must be at least 2 characters long.',
                    'name.max' => 'Name can not be more than 255 characters long.',

                    'age.required' => 'Age is required.',
                    'age.min' => 'Age must be a grater than 0.',

                    'email.required' => 'Email is required.',
                    'email.string' => 'Email must be a string.',
                    'email.email' => 'Email must be a valid email address.',
                    'email.unique' => 'Email already exists.',

                    'password.required' => 'Password is required.',
                    'password.string' => 'Password must be a string.',
                    'password.min' => 'Password must be at least 8 characters long.',
                    'password.confirmed' => 'Password confirmation does not match.',

                    'role.required' => 'Role is required.',
                    'role.in' => 'Role must be either Doctor or Patient.',
                ]
            );

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 0,
                        'message' => $validator->errors()->first()
                    ],
                    400
                );
            }

            $user = User::create([
                'name' => $formData['name'],
                'age' => $formData['age'],
                'email' => $formData['email'],
                'password' => Hash::make($formData['password']),
                'role' => $formData['role'],
            ]);

            $responseData = collect($user)->only(['name', 'age', 'email', 'role']);

            return response()->json(
                [
                    'status' => 1,
                    'message' => 'User registered successfully',
                    'data' => $responseData,
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => 'An error occurred during registration.',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    # Login user
    public function login(Request $request)
    {
        $formData = [
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ];

        $user = User::where('email', $request->email)->first();
        # Validate the form data
        $validator = Validator::make(
            $formData,
            [
                'email' => 'required|string|email|exists:users',
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
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 400);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized access email or password are invalid.'
            ], 401);
        }

        return $this->responseWithToken($token, $user);
    }

    # Refresh a token
    protected function responseWithToken($token, $user)
    {
        return response()->json([
            'status' => 1,
            'message' => 'Login successful',
            'user_data' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 200);
    }

    # Logout user
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => 1,
            'message' => 'User logged out successfully'
        ], 200);
    }

    # Reset password
    public function resetPassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'First you need to login to reset your password.'
            ], 404);
        }

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Old password is incorrect.'
            ], 400);
        }

        $formData = [
            'old_password' => $request->input('old_password'),
            'new_password' => $request->input('new_password'),
            'password_confirmation' => $request->input('confirm_new_password'),
        ];

        $validator = Validator::make(
            $formData,
            [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|different:old_password',
                'password_confirmation' => 'required|string|same:new_password',
            ],
            [
                'old_password.required' => 'Old password is required.',
                'old_password.string' => 'Old password must be a string.',

                'new_password.required' => 'New password is required.',
                'new_password.string' => 'New password must be a string.',
                'new_password.min' => 'New password must be at least 8 characters long.',
                'new_password.different' => 'New password must be different from old password.',

                'password_confirmation.required' => 'Confirm new password is required.',
                'password_confirmation.string' => 'Confirm new password must be a string.',
                'password_confirmation.same' => 'Confirm new password must match with new password.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['status' => 1, 'message' => 'Password reset successfully'], 200);
    }

    # Get Profile information
    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->role == "Patient") {
            # Retrieve the patient's record associated with the user
            $patient_about = Patient::select('contact_details', 'medical_history')->where('patient_id', $user->id)->first();

            if ($patient_about) {
                # Return the patient's data
                return response()->json([
                    'status' => 1,
                    'user_data' => $user,
                    'patient_about' => $patient_about,
                ], 200);
            } else {
                return response()->json([
                    'status' => 1,
                    'user_data' => $user,
                    'patient_about' => "No About Available",
                ], 200);
            }
        } else if ($user->role == "Doctor") {
            # Retrieve the doctor's record associated with the user
            $doctor_about = Doctor::select('contact_details', 'specialities', 'availability')->where('doctor_id', $user->id)->first();

            if ($doctor_about) {
                # Return the doctor's data
                return response()->json([
                    'status' => 1,
                    'user_data' => $user,
                    'doctor_about' => $doctor_about,
                ], 200);
            } else {
                return response()->json([
                    'status' => 1,
                    'user_data' => $user,
                    'doctor_about' => "No About Available",
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => "No data found",
            ], 404);
        }
    }

    # Update profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        # Check if the user is a Patient
        if ($user->role == 'Patient') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'age' => 'required|integer|min:1',
                'patient_contact_details' => 'nullable|string',
                'patient_medical_history' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            # Update the patient's basic profile in users table
            $user->name = $request->input('name');
            $user->age = $request->input('age');
            $user->save();

            # Retrieve the patient's record associated with the user
            $patient = Patient::where('patient_id', $user->id)->first();

            # If patient details alreay exists, update the fields, otherwise create a new patient details
            if ($patient) {
                if ($request->input('patient_contact_details') != '') {
                    $patient->contact_details = $request->input('patient_contact_details');
                }

                if ($request->input('patient_medical_history') != '') {
                    $patient->medical_history = $request->input('patient_medical_history');
                }

                $patient->save();
            } else {
                # If no patient exists, create a new one
                $patient = new Patient();
                $patient->patient_id = $user->id;

                # Assign values only if they're provided
                if ($request->input('patient_contact_details') != '') {
                    $patient->contact_details = $request->input('patient_contact_details');
                }

                if ($request->input('patient_medical_history') != '') {
                    $patient->medical_history = $request->input('patient_medical_history');
                }

                # Save the new patient record
                $patient->save();
            }
            # Return a response indicating the success
            return response()->json([
                'status' => 1,
                'message' => 'Patient profile updated successfully.',
                'user_data' => User::select('id', 'age', 'name', 'email', 'role')->where('id', $user->id)->first(),
                'patient_about' => Patient::select('contact_details', 'medical_history')->where('patient_id', $user->id)->first(),
            ], 200);
        }

        # Check if the user is a Doctor
        if ($user->role == 'Doctor') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'age' => 'required|integer|min:1',
                'doctor_contact_details' => 'nullable|string',
                'doctor_specialities' => 'nullable|array',
                'doctor_availability' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            # Update the doctor's basic profile in users table
            $user->name = $request->input('name');
            $user->age = $request->input('age');
            $user->save();

            # Retrieve the doctor's record associated with the user
            $doctor = Doctor::where('doctor_id', $user->id)->first();

            # If doctor details alreay exists, update the fields, otherwise create a new doctor details
            if ($doctor) {
                // dd(explode(",", preg_replace('/\s+/', '', $request->input('doctor_specialities'))));
                if ($request->has('doctor_contact_details')) {
                    $doctor->contact_details = $request->input('doctor_contact_details');
                }

                if ($request->has('doctor_specialities')) {
                    $doctor->specialities = json_encode($request->input('doctor_specialities'));
                    // $doctor->specialities = $request->input('doctor_specialities');
                }

                if ($request->input('doctor_availability') != '') {
                    $doctor->availability = $request->input('doctor_availability');
                }

                $doctor->save();
            } else {
                # If no doctor exists, create a new one
                $doctor = new Doctor();
                $doctor->doctor_id = $user->id;

                # Assign values only if they're provided
                if ($request->input('doctor_contact_details') != '') {
                    $doctor->contact_details = $request->input('doctor_contact_details');
                }

                if ($request->input('doctor_specialities') != '') {
                    $doctor->specialities = $request->input('doctor_specialities');
                }

                if ($request->input('doctor_availability') != '') {
                    $doctor->availability = $request->input('doctor_availability');
                }

                # Save the new doctor record
                $doctor->save();
            }
            # Return a response indicating the success
            return response()->json([
                'status' => 1,
                'message' => 'Doctor profile updated successfully.',
                'user_data' => User::select('id', 'age', 'name', 'email', 'role')->where('id', $user->id)->first(),
                'doctor_about' => Doctor::select('contact_details', 'specialities', 'availability')->where('doctor_id', $user->id)->first(),
            ], 200);
        }
    }
}
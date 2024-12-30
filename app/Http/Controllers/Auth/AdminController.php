<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Doctor;
use Auth;
use Illuminate\Contracts\Session\Session as SessionSession;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function adminLogin()
    {
        return view('admin.login');
    }

    public function adminLogout(Request $request)
    {       
        // Auth::logout();
        $request->session()->invalidate();
        return redirect()->intended('/admin/login');
    }
    
    public function checkLogin(LoginRequest $request)
    {

        // dd($request);
        
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if (empty($user)) {

            $flashArr = array(
                'msg' => 'User not found!',
            );

            return redirect()->route('admin.login')->with('err_message', $flashArr);
        }

        Auth::login($user);

        if (!empty(Auth::check()) && Auth::user()->role == 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        // if (Auth::attempt($credentials)) {
        //     $request->session()->regenerate();
        //     return redirect()->route('admin.dashboard');
        // } else {
        //     $message = 'Password incorrect!';
        //     $flashArr = array(
        //         'msg' => $message,
        //         'email' => $request->email
        //     );
        //     return redirect()->route('/admin/login')->with('err_message', $flashArr);
        // }


        // if(auth()->guard('web')->attempt(['email' => $request->input('email'),  'password' => $request->input('password')])){
        //     $user = auth()->guard('web')->user();
        //     if($user->role == "Admin"){
        //         $token = JWTAuth::fromUser($user);
        //         return redirect()->route('admin.dashboard')->with($token);
        //     }
        // }else {
        //     return back()->with('error','Whoops! invalid email and password.');
        // }

        // return back()
        //     ->withInput($request->only('email'))
        //     ->withErrors([
        //         'email' => 'You dont have permission to login here.',
        //     ]);


        {
            // Validate the request
            $credentials = $request->only('email', 'password');

            try {
                // Attempt to verify the credentials and create a token
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

            // Return the token to the client
            return response()->json(compact('token'));
        }
    }

    public function doctorRegister(){
        return view('admin.doctor_register');
    }

    public function adminDoctorRegister(DoctorRegisterRequest $request){
        // Register User(Doctor)
        try {
            $user = User::create([
                'name' => $request->name,
                'age' => $request->age,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Doctor',
            ]);

            // Doctor Details
            $doctor = Doctor::create([
                'doctor_id' => $user->id,
                'contact_details' => $request->contact_details,
                'specialties' => $request->specialties,
                'availability' => $request->availability,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Doctor registered successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred during registration.')->withInput();
        }
    }
}

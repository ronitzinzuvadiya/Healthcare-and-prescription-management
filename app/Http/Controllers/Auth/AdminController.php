<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Session\Session as SessionSession;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

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
    
    public function checkLogin(LoginRequest $request): RedirectResponse
    {

        // dd($request);
        
        // $credentials = $request->only('email', 'password');

        // $user = User::where('email', $request->email)->first();
        // if (empty($user)) {

        //     $flashArr = array(
        //         'msg' => 'User not found!',
        //     );

        //     return redirect()->route('admin.login')->with('err_message', $flashArr);
        // }

        // Auth::login($user);

        // if (!empty(Auth::check()) && Auth::user()->role == 'Admin') {
        //     return redirect()->route('admin.dashboard');
        // }

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

        
        if(auth()->guard('admin')->attempt(['email' => $request->input('email'),  'password' => $request->input('password')])){
            $user = auth()->guard('admin')->user();
            if($user->role == "Admin"){
                return redirect()->route('admin.dashboard')->with('success','You are Logged in sucessfully.');
            }
        }else {
            return back()->with('error','Whoops! invalid email and password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'You dont have permission to login here.',
            ]);
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
        // Login User
}

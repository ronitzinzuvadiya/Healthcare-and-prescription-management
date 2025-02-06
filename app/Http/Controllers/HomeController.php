<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;

class HomeController extends Controller
{
    public function dashboard(){
        if (auth()->user()->role === 'Admin') {
            $doctorCount = User::where('role', 'Doctor')->count();
            $patientCount = User::where('role', 'Patient')->count();
            return view('admin.dashboard', compact('doctorCount', 'patientCount'));
        }
    
        if(auth()->user()->role === 'Doctor'){
            return view('doctor.dashboard');
        }

        if(auth()->user()->role === 'Patient'){
            return view('patient.dashboard');
        }

        Auth::logout();
        return redirect()->route('admin.login')->with('error', 'Unauthorized access.');
    }

    public function destroy(){
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // dd($request); # email and password come in request parameters
        $credentials = $request->only('email', 'password');
        // $token = Auth::attempt(($credentials));

        // if ($token) {
        //     return redirect()->intended(RouteServiceProvider::DASHBOARD);
        // }
        // return redirect()->back()->withErrors('Invalid credentials');

        // dd(Auth::attempt($credentials));
        if (Auth::attempt($credentials)) {
            // dd(auth()->user()->role);
            if (auth()->user()->role === 'Admin') {
                return redirect()->route('admin.dashboard');
            }
	    
            if(auth()->user()->role === 'Doctor'){
                return redirect()->route('doctor.dashboard');
            }

            if(auth()->user()->role === 'Patient'){
                return redirect()->route('patient.dashboard');
            }

            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Unauthorized access.');
        }
        return redirect()->back()->withErrors('Invalid credentials');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

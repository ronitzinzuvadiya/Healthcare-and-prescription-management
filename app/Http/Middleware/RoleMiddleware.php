<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        # if (Auth::user() && Auth::user()->role == 'Admin') { # Check the user's role directly
        #     return $next($request);
        # } else {
        #     return redirect()->route('login')->with('error', 'You do not have permission to access this page!');
        # }

        // dd(auth()->user()->role);
        if (auth()->check())
        {
            if(auth()->user()->role === 'Admin') {
                return $next($request);
            } else {
                return route('login');
            }
        }
    
        // return redirect('/home')->with('error', 'Unauthorized access.');
    }
}

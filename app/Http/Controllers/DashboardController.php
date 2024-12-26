<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $doctorCount = User::where('role', 'Doctor')->count();
        $patientCount = User::where('role', 'Patient')->count();

        return view('admin.dashboard', compact('doctorCount', 'patientCount'));
    }
}

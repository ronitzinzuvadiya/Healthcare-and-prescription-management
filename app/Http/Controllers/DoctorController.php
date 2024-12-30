<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DoctorController extends Controller
{
    public function index(){
        // Fetch all doctors from the database and return them in a view
        $doctors = User::where('role', 'Doctor')->get();
        return view('admin.doctors', compact('doctors'));
    }
}

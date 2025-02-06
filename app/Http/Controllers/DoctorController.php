<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRegisterRequest;
use App\Http\Requests\DoctorUpdateRequest;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function dashboard()
    {
        if (!auth()->user()) {  
            return redirect('/login');
        }
        return view('doctor.dashboard');
    }

    public function doctorList()
    {
        # Fetch all doctors from the database and return them in a view
        $doctors = User::where('role', 'Doctor')->with('doctor_details')->get();
        return view('admin.doctor.list', compact('doctors'));
    }

    public function doctorRegister()
    {
        return view('admin.doctor.register');
    }

    public function adminDoctorRegister(DoctorRegisterRequest $request)
    {
        # Register User(Doctor)
        try {
            $doctor = User::create([
                'name' => $request->name,
                'age' => $request->age,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Doctor',
            ]);

            return redirect()->route('doctor.list', compact('doctor'));
        } catch (\Exception $e) {
            return back()->with('message', 'An error occurred during registration.')->withInput();
        }
    }

    public function adminDoctorEdit($id)
    {
        $doctor = User::with('doctor_details')->find($id);

        # dd($doctor);
        if ($doctor) {
            return view('admin.doctor.edit', compact('doctor'));
        }

        return back()->with('message', 'Doctor not found.');
    }

    public function adminDoctorUpdate(DoctorUpdateRequest $request, $id)
    {
        $doctor = User::find($id);

        if (!$doctor) {
            return back()->with('message', 'Doctor not found.');
        }

        $doctor->name = $request->name;
        $doctor->age = $request->age;
        $doctor->save();

        $doctorDetails = $doctor->doctor_details;

        if (!$doctorDetails) {
            $doctorDetails = new Doctor();
            $doctorDetails->doctor_id = $doctor->id;
        }

        if ($request->contact_details !== null) {
            $doctorDetails->contact_details = $request->contact_details;
        }

        if ($request->availability !== null) {
            $doctorDetails->availability = $request->availability;
        }

        $doctorDetails->save();

        return redirect()->route('doctor.list')->with('message', 'Doctor updated successfully.');
    }

    public function adminDoctorDelete($id)
    {
        $doctor = User::find($id);

        if ($doctor) {
            $doctorDetails = $doctor->doctor_details;

            if ($doctorDetails) {
                $doctorDetails->delete();
            }

            $doctor->delete();
            return back()->with('message', 'Doctor deleted successfully.');
        }

        return back()->with('message', 'Doctor not found.');
    }

    public function getPrescriptionsList(){
        # Fetch prescriptions for the current doctor
        $user = Auth::user();

        if($user->role == "Admin"){
            $prescriptions = Prescription::with('patient', 'doctor')->get();
        } elseif($user->role == 'Doctor'){
            $prescriptions = Prescription::with('patient', 'doctor')->where('doctor_id', $user->id)->get();
        } else {
            return redirect()->route('doctor.dashboard')->with('message', 'Only Doctor and Admin can view prescriptions.');
        }

        return view('doctor.prescriptions', compact('prescriptions'));
    }

    public function DoctorPrescriptionDelete($id)
    {
        $prescription = Prescription::find($id);

        if ($prescription) {
            $prescription->delete();
            return back()->with('message', 'Prescription deleted successfully.');
        }

        return back()->with('error', 'Prescription not found.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRegisterRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function dashboard()
    {
        if (!auth()->user()) {
            return redirect('/login');
        }
        return view('patient.dashboard');
    }

    public function patientList()
    {
        $user = auth()->user();
        
        if ($user->role === 'Admin') {
            $patients = User::where('role', 'Patient')->with('patient')->get();

            return view('admin.patient.list', compact('patients'));
        }

        if ($user->role === 'Doctor') {
            $appointments = Appointment::with('patient')->where('doctor_id', $user->id)->get();

            $patients = [];

            foreach ($appointments as $appointment) {
                $patients[] = $appointment->patient;
            }

            $patients = collect($patients)->unique('id')->values();

            return view('admin.patient.list', compact('patients'));
        }

        return back()->with('message', 'You are not authorized to view this page.');
    }

    public function patientRegister()
    {
        return view('admin.patient.register');
    }

    public function adminPatientRegister(PatientRegisterRequest $request)
    {
        # Register User(Patient)
        try {
            $patient = User::create([
                'name' => $request->name,
                'age' => $request->age,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Patient',
            ]);

            return redirect()->route('patient.list', compact('patient'));
        } catch (\Exception $e) {
            return back()->with('message', 'An error occurred during registration.')->withInput();
        }
    }

    public function editPatient($id)
    {
        $patient =  User::with('patient')->find($id);

        if ($patient) {
            return view('admin.patient.edit', compact('patient'));
        }
        return back()->with('message', 'Patient not found.');
    }

    public function updatePatient(PatientUpdateRequest $request, $id)
    {
        $patient = User::with('patient')->find($id);

        if ($patient) {
            $patient->name = $request->name;
            $patient->age = $request->age;
            $patient->save();

            $patientDetails = $patient->patient;

            if (!$patientDetails) {
                if ($request->contact_details !== null || $request->medical_history !== null) {
                    $patientDetails = new Patient();
                    $patientDetails->patient_id = $patient->id;

                    if ($request->contact_details !== null) {
                        $patientDetails->contact_details = $request->contact_details;
                    }
                    if ($request->medical_history !== null) {
                        $patientDetails->medical_history = $request->medical_history;
                    }
                    $patientDetails->save();
                }
            } else {
                $patientDetails->contact_details = $request->contact_details;
                $patientDetails->medical_history = $request->medical_history;
                $patientDetails->save();
            }

            return redirect()->route('patient.list')->with('message', 'Patient updated successfully.');
        }
        return back()->with('message', 'Patient not found.');
    }

    public function patientDelete($id)
    {
        $patient = User::find($id);

        if ($patient) {
            $patientDetails = $patient->patient;

            if ($patientDetails) {
                $patientDetails->delete();
            }

            $patient->delete();
            return back()->with('success', 'Patient deleted successfully.');
        }

        return back()->with('message', 'Patient not found.');
    }
}

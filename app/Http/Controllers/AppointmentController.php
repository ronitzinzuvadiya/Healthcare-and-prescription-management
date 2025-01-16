<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorUpdateAppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function AppointmentsList()
    {
        $user = auth()->user();

        if ($user->role === 'Admin') {
            $appointments = Appointment::with('patient', 'doctor')->orderBy('appointment_date', 'asc')->get();
            $doctors = $appointments->pluck('doctor')->unique('id');
            $patients = $appointments->pluck('patient')->unique('id');

            return view('admin.appointment.list', compact('appointments', 'doctors', 'patients'));
        } elseif ($user->role === 'Doctor') {
            $appointments = Appointment::where('doctor_id', $user->id)->with('patient', 'doctor')->orderBy('appointment_date', 'asc')->get();

            return view('doctor.appointment.list', compact('appointments'));
        } elseif ($user->role === 'Patient') {
            $appointments = Appointment::where('patient_id', $user->id)->with('patient', 'doctor')->orderBy('appointment_date', 'asc')->get();
            return view('patient.appointment.list', compact('appointments'));
        }
    }

    public function doctorEditAppointment($id)
    {
        $appointment = Appointment::with('patient', 'doctor')->find($id);

        if ($appointment) {
            # Ensure start_time and end_time are formatted correctly (HH:MM)
            $appointment->start_time = \Carbon\Carbon::parse($appointment->start_time)->format('H:i');
            $appointment->end_time = \Carbon\Carbon::parse($appointment->end_time)->format('H:i');

            return view('doctor.appointment.edit', compact('appointment'));
        }

        return back()->with('error', 'Appointment not found.');
    }

    public function doctorUpdateAppointment(DoctorUpdateAppointmentRequest $request, $id)
    {
        $appointment = Appointment::find($id);

        if ($appointment) {
            $appointment->appointment_date = $request->input('appointment_date');
            $appointment->start_time = $request->input('start_time');
            $appointment->end_time = $request->input('end_time');
            $appointment->status = $request->input('status');
            $appointment->save();
            return redirect()->route('appointments.list')->with('message', 'Appointment updated successfully.');
        }

        return back()->with('error', 'Appointment not found.');
    }

    public function doctorDeleteAppointment($id)
    {
        $appointment = Appointment::find($id);

        if ($appointment) {
            $appointment->delete();
            return redirect()->route('appointments.list')->with('message', 'Appointment deleted successfully.');
        }

        return back()->with('error', 'Appointment not found.');
    }
}

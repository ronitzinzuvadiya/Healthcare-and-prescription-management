<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentDetailsResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use Auth;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function filterDoctors(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the login user is a Patient
        if ($user->role == 'Patient') {
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_specialities' => 'required',
                    'doctor_availability' => 'required',
                ],
                [
                    'doctor_specialities.required' => 'Specialties are required.',
                    'doctor_availability.required' => 'Please select availability.'
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $doctorSpecialities = $request->input('doctor_specialities');
            # $doctorAvailability = $request->input('doctor_availability');

            foreach ($doctorSpecialities as $ds) {
                $doctors = Doctor::with('doctorDetails:id,name')->select('id', 'doctor_id', 'contact_details', 'specialities', 'availability')->where('specialities', 'LIKE', '%' . $ds . '%')->get();
            }

            if ($doctors->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No doctors found matching your criteria.'
                ], 200);
            }

            return response()->json([
                'status' => 0,
                'message' => "Doctor Fetched Successfully.",
                'doctors' => $doctors
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => "Only Patients can filter doctors.",
            ], 403);
        }
    }

    public function bookAppointment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the login user is a Patient
        if ($user->role == 'Patient') {
            $validator = Validator::make(
                $request->all(),
                [
                    'problems' => 'required|array',
                    'doctor_id' => 'required',
                    'appointment_date' => 'required',
                    'start_time' => 'required',
                    'end_time' => 'required',
                ],
                [
                    'problems.required' => 'Please select problems.',
                    'doctor_id.required' => 'Please select doctor.',
                    'appointment_date.required' => 'Appointment Date is required.',
                    'start_time.required' => 'Start Time is required.',
                    'end_time.required' => 'End Time is required.'
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $problems = $request->input('problems');
            $doctorId = $request->input('doctor_id');
            $appointmentDate = $request->input('appointment_date');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            $doctor = Doctor::with('doctorDetails')->where('doctor_id', $doctorId)->first();
            # dd($doctor);
            # dd($problems);
            foreach ($problems as $p) {
                if ($p != null) {
                    if (!in_array($p, $doctor->specialities)) {
                        return response()->json([
                            'status' => 0,
                            'message' => 'Doctor does not specialize in the selected problem.'
                        ], 400);
                    } else {
                        # Check if the doctor has any other appointments scheduled on the same date and at overlapping times
                        $existingAppointment = Appointment::where('doctor_id', $doctorId)
                            ->where('appointment_date', $appointmentDate)
                            ->where(function ($query) use ($startTime, $endTime) {
                                $query->whereBetween('start_time', [$startTime, $endTime])
                                    ->orWhereBetween('end_time', [$startTime, $endTime])
                                    ->orWhere(function ($query) use ($startTime, $endTime) {
                                        $query->where('start_time', '<=', $startTime)
                                            ->where('end_time', '>=', $endTime);
                                    });
                            })
                            ->exists();

                        if ($existingAppointment) {
                            return response()->json([
                                'status' => 0,
                                'message' => 'Doctor has an appointment scheduled during the given time.'
                            ], 400);
                        } else {
                            # Create the appointment record
                            $appointment = new Appointment();
                            $appointment->patient_id = $user->id;
                            $appointment->doctor_id = $doctorId;
                            $appointment->appointment_date = $appointmentDate;
                            $appointment->start_time = $startTime;
                            $appointment->end_time = $endTime;
                            $appointment->problems = implode(',', $problems);
                            $appointment->status = 'pending';
                            $appointment->save();

                            $appointmentDetails = new AppointmentDetailsResource($appointment);

                            return response()->json([
                                'status' => 1,
                                'message' => 'Appointment booked successfully.',
                                'appointment_details' => $appointmentDetails,
                            ], 200);
                        }
                    }
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only patients can book appointments.'
            ], 401);
        }
    }

    public function getPrescriptionsList()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the login user is a Patient
        if ($user->role == 'Patient') {
            $prescriptions = Prescription::where('patient_id', $user->id)->get();

            if ($prescriptions->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No prescriptions found.'
                ], 200);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Prescriptions fetched successfully.',
                'prescriptions' => $prescriptions
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only patients can view prescriptions.'
            ], 401);
        }
    }
}

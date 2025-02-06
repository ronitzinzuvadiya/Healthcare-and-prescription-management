<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentDetailsResource;
use App\Http\Resources\PatientPrescriptionResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use Auth;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    # Doctor Availability Update
    public function updateDoctorAvailability(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role == 'Doctor') {
            $validator = Validator::make($request->all(), [
                'doctor_availability' => 'required',
            ]);

            # Validate the incoming request
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            # Retrieve the doctor's record associated with the user
            $doctor = Doctor::where('doctor_id', $user->id)->first();

            if ($doctor) {
                $doctor->availability = $request->input('doctor_availability');
                $doctor->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Doctor availability updated successfully.',
                    'doctor_about' => Doctor::select('contact_details', 'specialities', 'availability')->where('doctor_id', $user->id)->first(),
                ], 200);
            } else {
                $doctor = new Doctor();
                $doctor->doctor_id = $user->id;

                if ($request->input('doctor_availability') != '') {
                    $doctor->availability = $request->input('doctor_availability');
                }

                # Save the new doctor record
                $doctor->save();

                return response()->json([
                    'status' => 1,
                    'message' => 'Doctor availability updated successfully.',
                    'doctor_about' => Doctor::select('contact_details', 'specialities', 'availability')->where('doctor_id', $user->id)->first(),
                ], 200);
            }
            return response()->json([
                'status' => 0,
                'message' => 'Doctor not found.',
            ], 404);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Only Doctor can update their availability.',
        ], 401);
    }

    # Doctor Appointment Update
    public function updateAppointment(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role != 'Doctor') {
            return response()->json([
                'status' => 0,
                'message' => 'Only Doctor can update their appointment.',
            ], 401);
        }

        # Validate the incoming request
        $validator = Validator::make(
            $request->all(),
            [
                'patient_id' => 'required',
                'appointment_id' => 'required',
                'appointment_date' => 'required',
                'start_time' => 'sometimes|required',
                'end_time' => 'sometimes|required',
                'status' => 'required|in:pending,approved,rejected,rescheduled,completed',
            ],
            [
                'patient_id.required' => 'Patient ID must be provided',
                'appointment_id.required' => 'Please enter appointment id.',
                'appointment_date.required' => 'Appointment Date is required.',
                'start_time.required' => 'Start Time is required.',
                'end_time.required' => 'End Time is required.',
                'status.required' => 'Status is required.',
                'status.in' => 'Status must be one of the following: pending, approved, rejected, rescheduled, completed.',
            ]
        );

        # If validation fails, return error message
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        # Retrieve the appointment details
        $appointment = Appointment::find($request->input('appointment_id'));

        # Check if the appointment exists
        if (!$appointment) {
            return response()->json([
                'status' => 0,
                'message' => 'Appointment not found.',
            ], 404);
        }

        # Check if the logged-in doctor is the one assigned to the appointment
        if ($appointment->doctor_id !== $user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'You are not authorized to update this appointment.',
            ], 403);
        }

        # Check if the patient ID matches
        if ($appointment->patient_id != $request->input('patient_id')) {
            return response()->json([
                'status' => 0,
                'message' => 'Patient ID does not match the appointment.',
            ], 400);
        }

        # Check if the doctor has any overlapping appointments for other patients
        $overlappingAppointment = Appointment::where('doctor_id', $user->id)
            ->where('appointment_date', $request->input('appointment_date'))
            ->where(function ($query) use ($request, $appointment) {
                # Check for overlapping time but exclude the current appointment's patient
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->whereBetween('start_time', [$request->input('start_time'), $request->input('end_time')])
                        ->orWhereBetween('end_time', [$request->input('start_time'), $request->input('end_time')]);
                })
                    ->where('patient_id', '!=', $appointment->patient_id); # Ensure it's not the same patient
            })
            ->exists();

        if ($overlappingAppointment) {
            return response()->json([
                'status' => 0,
                'message' => 'You already have another appointment scheduled during the given time for a different patient.',
            ], 400);
        }

        # Update appointment details
        $appointment->appointment_date = $request->input('appointment_date', $appointment->appointment_date);
        $appointment->start_time = $request->input('start_time', $appointment->start_time);
        $appointment->end_time = $request->input('end_time', $appointment->end_time);
        $appointment->status = $request->input('status', $appointment->status);

        # Save updated appointment
        $appointment->save();

        # Return the updated appointment details
        $appointmentDetails = new AppointmentDetailsResource($appointment);
        return response()->json([
            'status' => 1,
            'message' => 'Appointment updated successfully.',
            'appointment_details' => $appointmentDetails,
        ], 200);
    }

    # Doctor can see their appointments
    public function getAppointmentsList()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role == 'Doctor') {
            $appointments = Appointment::where('doctor_id', $user->id)->orderBy('appointment_date', 'asc')->get();

            if ($appointments->isNotEmpty()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Appointments retrieved successfully.',
                    'appointments' => $appointments,
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'No appointments found.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only Doctor can view their appointments.',
            ], 401);
        }
    }

    # Doctor can create prescriptions for patients
    public function createPrescriptions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role == 'Doctor') {
            $validator = Validator::make(
                $request->all(),
                [
                    'appointment_id' => 'required',
                    'medicine_name' => 'required',
                    'dosage' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                ],
                [
                    'appointment_id.required' => 'Appointment ID must be provided',
                    'medicine_name.required' => 'Medicine Name is required.',
                    'dosage.required' => 'Dosage is required.',
                    'start_date.required' => 'Start Date is required.',
                    'end_date.required' => 'End Date is required.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $patientAppointment = Appointment::find($request->input('appointment_id'));
            # dd($patientAppointment->doctor_id);

            if ($patientAppointment) {
                if ($patientAppointment->doctor_id === $user->id) {
                    $prescription = new Prescription();
                    $prescription->patient_id = $patientAppointment->patient_id;
                    $prescription->doctor_id = $user->id;
                    $prescription->medicine_name = $request->input('medicine_name');
                    $prescription->dosage = $request->input('dosage');
                    $prescription->start_date = $request->input('start_date');
                    $prescription->end_date = $request->input('end_date');
                    $prescription->special_notes = $request->input('special_notes');
                    $prescription->save();

                    $prescriptionDetails = new PatientPrescriptionResource($prescription);

                    return response()->json([
                        'status' => 1,
                        'message' => 'Prescription created successfully.',
                        'prescription_details' => $prescriptionDetails,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'You are not authorized to create prescriptions for this appointment.',
                    ], 403);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Appointment not found.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only Doctor can create prescriptions.',
            ], 401);
        }
    }

    # Doctor can update prescription of their patients
    public function updatePrescription(Request $request, $prescription_id)
    {
        # dd($prescription_id);
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role == 'Doctor') {
            $validator = Validator::make(
                $request->all(),
                [
                    'medicine_name' => 'required',
                    'dosage' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                ],
                [
                    'medicine_name.required' => 'Medicine Name is required.',
                    'dosage.required' => 'Dosage is required.',
                    'start_date.required' => 'Start Date is required.',
                    'end_date.required' => 'End Date is required.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $prescription = Prescription::find($prescription_id);

            if ($prescription) {
                if ($prescription->doctor_id === $user->id) {
                    $prescription->medicine_name = $request->input('medicine_name');
                    $prescription->dosage = $request->input('dosage');
                    $prescription->start_date = $request->input('start_date');
                    $prescription->end_date = $request->input('end_date');
                    $prescription->special_notes = $request->input('special_notes');
                    $prescription->save();

                    $prescriptionDetails = new PatientPrescriptionResource($prescription);
                    # dd($prescriptionDetails);

                    return response()->json([
                        'status' => 1,
                        'message' => 'Prescription updated successfully.',
                        'prescription_details' => $prescriptionDetails,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'You are not authorized to update this prescription.',
                    ], 403);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Prescription not found.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only Doctor can update prescriptions.',
            ], 401);
        }
    }

    # Doctor can delete prescription of their patients
    public function deletePrescription($prescription_id)
    {
        # dd($prescription_id);
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Session ended, Please login again.',
            ], 401);
        }

        # Check if the logged-in user is a Doctor
        if ($user->role == 'Doctor') {
            $prescription = Prescription::find($prescription_id);

            if ($prescription) {
                if ($prescription->doctor_id === $user->id) {
                    $prescription->delete();

                    return response()->json([
                        'status' => 1,
                        'message' => 'Prescription deleted successfully.',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'You are not authorized to delete this prescription.',
                    ], 403);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Prescription not found.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Only Doctor can delete prescriptions.',
            ], 401);
        }
    }
}

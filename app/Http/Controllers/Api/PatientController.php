<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Auth;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function filterDoctors(Request $request)
    {
        $user = Auth::user();

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
            // $doctorAvailability = $request->input('doctor_availability');

            foreach ($doctorSpecialities as $ds){                
                $doctors = Doctor::with('doctorDetails:id,name')->select('id','doctor_id','contact_details','specialities','availability')->where('specialities', 'LIKE', '%'.$ds.'%')->get();
            }

            // dd($doctors);
            if($doctors->isEmpty()){
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
        }
    }
}
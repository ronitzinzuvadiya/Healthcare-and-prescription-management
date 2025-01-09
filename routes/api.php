<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\DoctorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api'], function ($routes) {
    Route::post('/register', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('resetpassword');

    # Profile Update API for both patient and doctor role (different field value need to submit based on user role)
    Route::post('profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    # Patient can Filter Doctor based on their speciality and availability
    Route::post('filter/doctors', [PatientController::class, 'filterDoctors'])->name('filter.doctors');

    # Patient can book apointments based on doctor availability
    Route::post('book/appointment', [PatientController::class, 'bookAppointment'])->name('book.appointment');

    # Doctor can update their availability 
    Route::post('availability/update', [DoctorController::class, 'updateDoctorAvailability'])->name('availability.update');

    # Doctor can approve, reject, or reschedule appointments
    Route::post('appointment/update', [DoctorController::class, 'updateAppointment'])->name('appointment.update');

    # Doctor can see their appointments list
    Route::get('getAppointments', [DoctorController::class, 'getAppointmentsList'])->name('get.appointments.list');

    # Doctor can create prescriptions for patients
    Route::post('prescription/create', [DoctorController::class, 'createPrescriptions'])->name('prescription.create');

    # Doctor can update prescriptions for their patients
    Route::post('prescription/update/{id}', [DoctorController::class, 'updatePrescription'])->name('prescription.update');

    # Doctor can delete prescriptions for their patients
    Route::delete('prescription/delete/{id}', [DoctorController::class, 'deletePrescription'])->name('prescription.delete');

    # Patient can view and download prescriptions
    Route::get('prescriptions', [PatientController::class, 'getPrescriptionsList'])->name('get.prescriptions.list');
});

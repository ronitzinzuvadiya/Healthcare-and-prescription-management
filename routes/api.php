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


    // Patient Registration
    // Route::post('/register-patient', [PatientController::class, 'registerPatient'])->name('register.patient');
    
    // Patient can edit their own profile (if logged-in user is patient)
    // Route::post('patient/profile/update', [PatientController::class, 'updateProfile'])->name('patient.profile.update');

    // Admin can register doctor (if logged-in user is Admin, then it can register doctor [userstable = name, age, email, password, role  & 'doctor_details' table = specialties, availability]
    // 

    # Profile Update API for both patient and doctor role (different field value need to submit based on role)
    Route::post('profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    # Patient can Filter Doctor based on their speciality and availability
    Route::post('filter/doctors', [PatientController::class, 'filterDoctors'])->name('filter.doctors');
});


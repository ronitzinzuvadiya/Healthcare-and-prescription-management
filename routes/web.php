<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.post');

Route::post('/logout', function () {
    return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
})->name('admin.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/admin/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', [HomeController::class, 'destroy'])->name('admin.logout');

    # Doctor Module (Admin)
    Route::get('/doctors', [DoctorController::class, 'doctorList'])->name('doctor.list');
    Route::get('/doctor/register', [DoctorController::class, 'doctorRegister'])->name('doctor.register');
    Route::post('/doctor/register', [DoctorController::class, 'adminDoctorRegister'])->name('admin.doctor.register');
    Route::get('/doctor/{id}/edit', [DoctorController::class, 'adminDoctorEdit'])->name('admin.doctor.edit');
    Route::post('/doctor/{id}', [DoctorController::class, 'adminDoctorUpdate'])->name('admin.doctor.update');
    Route::delete('/doctor/{id}', [DoctorController::class, 'adminDoctorDelete'])->name('admin.doctor.delete');
    
    # Patient Module (Admin)
    Route::get('/patients', [PatientController::class, 'patientList'])->name('patient.list');
    Route::get('/patient/register', [PatientController::class, 'patientRegister'])->name('patient.register');
    Route::post('/patient/register', [PatientController::class, 'adminPatientRegister'])->name('admin.patient.register');
    Route::get('/patient/{id}/edit', [PatientController::class, 'editPatient'])->name('admin.patient.edit');
    Route::post('/patient/{id}', [PatientController::class, 'updatePatient'])->name('admin.patient.update');    
    Route::delete('/patient/{id}', [PatientController::class, 'patientDelete'])->name('admin.patient.delete');
    
    # Doctor Module (Doctor)
    Route::get('/doctor/dashboard', [HomeController::class, 'dashboard'])->name('doctor.dashboard');

    # Patient Module (Patient)
    Route::get('/patient/dashboard', [HomeController::class, 'dashboard'])->name('patient.dashboard');
    
    # Appointment Module (Admin, Doctor, Patient)
    Route::get('/appointments', [AppointmentController::class, 'AppointmentsList'])->name('appointments.list');
    Route::get('/appointment/{id}/edit', [AppointmentController::class, 'doctorEditAppointment'])->name('doctor.appointment.edit');
    Route::post('/appointment/{id}', [AppointmentController::class, 'doctorUpdateAppointment'])->name('doctor.appointment.update');
    Route::delete('/appointment/{id}', [AppointmentController::class, 'doctorDeleteAppointment'])->name('doctor.appointment.delete');

    # Prescription Module (Doctor)
    Route::get('/prescriptions', [DoctorController::class, 'getPrescriptionsList'])->name('prescription.list');
    Route::delete('/prescription/{id}', [DoctorController::class, 'DoctorPrescriptionDelete'])->name('doctor.prescription.delete');
});
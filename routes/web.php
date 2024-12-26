<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;

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

Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');
Route::post('/check-login',[AdminController::class, 'checkLogin'])->name('check-login');
Route::post('/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');

Route::group(['middleware' => 'admin'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctor.index');
    Route::get('/doctor/register', [AdminController::class, 'doctorRegister'])->name('doctor.register');
    Route::post('/doctor/register', [AdminController::class, 'adminDoctorRegister'])->name('admin.doctor.register');
});
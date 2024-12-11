<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Route::get('/admin', function () {
//     return view('admin.dashboard');
// });

// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

Route::get('/login', [AuthController::class, 'login'])->name('user.login');
Route::post('/login', [AuthController::class, 'checkLogin'])->name('check.login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
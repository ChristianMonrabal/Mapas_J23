<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('signin', [AuthController::class, 'showSigninForm'])->name('auth.signin');
Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin.submit');
Route::get('signup', [AuthController::class, 'showSignupForm'])->name('auth.signup');
Route::post('signup', [AuthController::class, 'signup'])->name('auth.signup.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/', function () {
    return view('index');
})->name('index');

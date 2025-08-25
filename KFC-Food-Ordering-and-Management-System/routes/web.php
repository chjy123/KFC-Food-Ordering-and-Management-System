<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', fn () => view('home'))->name('home');

/* Guest-only routes */
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.store');

    Route::get('/signin', [UserController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.store');
});

/* Auth-only routes */
Route::middleware('auth')->group(function () {
    // Optional protected page
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Logout must be POST
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

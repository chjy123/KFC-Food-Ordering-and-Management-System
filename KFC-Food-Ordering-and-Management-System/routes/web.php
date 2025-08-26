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

/* Auth only */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Admin page (only if role = admin)
    Route::get('/admin', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized'); // block non-admins
        }
        return view('admin'); // resources/views/admin.blade.php
    })->name('admin.page');
});

/*Menu route*/
use App\Http\Controllers\MenuController;

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

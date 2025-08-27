<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;

//* Home -> resources/views/User/home.blade.php */
Route::get('/', fn () => view('User.home'))->name('home');

/* -------- Guest-only -------- */
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.store');

    Route::get('/signin', [UserController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.store');
});

/* -------- Auth-only -------- */
Route::middleware('auth')->group(function () {
    // IMPORTANT: logout route (POST)
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Customer dashboard + updates
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::put('/dashboard', [UserController::class, 'updateProfile'])->name('dashboard.update');
    Route::put('/dashboard/password', [UserController::class, 'updatePassword'])->name('dashboard.password');

    // Admin landing (view lives at resources/views/Admin/index.blade.php)
    Route::get('/admin', function () {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        return view('Admin.index');
    })->name('admin.page');
});

/* Menu */
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;

/* Home uses your User/home.blade.php */
Route::get('/', fn () => view('User.home'))->name('home');

/* Guest-only routes */
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.store');

    Route::get('/signin', [UserController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.store');
});

/* Auth-only routes */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Admin page (only if role = admin) – placeholder text until you create a Blade
    Route::get('/admin', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        return 'Admin page'; // later: return view('Admin.index');
    })->name('admin.page');
});

/* Menu */
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

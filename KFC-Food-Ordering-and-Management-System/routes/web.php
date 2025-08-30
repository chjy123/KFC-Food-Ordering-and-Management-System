<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Web\PaymentWebController;

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

#author’s name： Pang Jun Meng
/* Payment Routes */
Route::middleware(['auth'])->group(function () {
    // Customer checkout page (GET) and form submit (POST)
    Route::get('/payments/checkout/{orderId}', [PaymentWebController::class, 'showCheckout'])->name('payments.checkout');
    Route::post('/payments/checkout', [PaymentWebController::class, 'processCheckout'])->name('payments.checkout.process');

    // Customer history
    Route::get('/payments/history', [PaymentWebController::class, 'history'])->name('payments.history');

    // Show success/fail pages (optional redirect targets)
    Route::get('/payments/success/{id}', [PaymentWebController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed', [PaymentWebController::class, 'failed'])->name('payments.failed');
});

// Admin routes - ensure you protect with proper middleware/gate in production
Route::middleware(['auth','can:refund-payments'])->group(function () {
    Route::get('/admin/payments', [PaymentWebController::class, 'adminHistory'])->name('admin.payments');
   // Route::get('/admin/payments/{id}/refund', [PaymentWebController::class, 'showRefundForm'])->name('admin.payments.refund.form');
    //Route::post('/admin/payments/{id}/refund', [PaymentWebController::class, 'postRefund'])->name('admin.payments.refund');
});

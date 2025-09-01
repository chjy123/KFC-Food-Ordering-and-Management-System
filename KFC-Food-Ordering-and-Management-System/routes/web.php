<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;

//* Home -> resources/views/User/home.blade.php */
Route::get('/', fn () => view('User.home'))->name('home');

/* -------- Guest-only -------- */
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.store');

    Route::get('/signin', [UserController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.store');
});

// Put this in web.php (near your auth routes)
Route::get('/login', fn () => redirect()->route('login.show'))->name('login');

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
Route::get('/menu/{food}', [MenuController::class, 'show'])->name('menu.show'); // Food detail

// Food CRUD (create/update/delete)
Route::post('/foods', [MenuController::class, 'storeFood'])->name('foods.store');
Route::put('/foods/{food}', [MenuController::class, 'updateFood'])->name('foods.update');
Route::delete('/foods/{food}', [MenuController::class, 'destroyFood'])->name('foods.destroy');

// Review CRUD (add/update/delete) - scoped to a Food where handy
Route::post('/foods/{food}/reviews', [MenuController::class, 'storeReview'])->name('reviews.store');
Route::put('/reviews/{review}', [MenuController::class, 'updateReview'])->name('reviews.update');
Route::delete('/reviews/{review}', [MenuController::class, 'destroyReview'])->name('reviews.destroy');

//Location page
Route::get('/kfc-locations', [LocationController::class, 'index'])->name('kfc.locations');

//about page
Route::view('/about', 'user.about')->name('about');

Route::middleware('auth')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Orders
    Route::post('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});


Route::delete('/foods/{food}/reviews/mine', [ReviewController::class, 'destroyMine'])
    ->name('reviews.destroy.mine');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\CategoryController;   
use App\Http\Controllers\Admin\FoodController;    
use App\Http\Controllers\Admin\AdminReviewController; 
use App\Http\Controllers\Admin\AdminReportController;    

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

    //       Route::get('/admin', function () {
    //         if (! auth()->user()->isAdmin()) {
    //             abort(403, 'Unauthorized');
    //         }
    //         return view('Admin.index');
    //     })->name('admin.page');
    // });
    });
 Route::prefix('admin')->name('admin.')->group(function () {
    
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu');

        Route::resource('foods', FoodController::class)->except(['index','show']);
        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/reports/download', [AdminReportController::class, 'download'])->name('reports.download');
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


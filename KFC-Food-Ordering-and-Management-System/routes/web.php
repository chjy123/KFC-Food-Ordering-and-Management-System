<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\CategoryController;   
use App\Http\Controllers\Admin\FoodController;    
use App\Http\Controllers\Admin\AdminReviewController; 
use App\Http\Controllers\Admin\AdminReportController;  
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\PaymentWebController;
use App\Http\Controllers\StripeController;

//* Home -> resources/views/User/home.blade.php */
Route::get('/', fn () => view('User.home'))->name('home');

/* Guest-only*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [UserController::class, 'register'])->name('register.store');

    Route::get('/signin', [UserController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [UserController::class, 'login'])->name('login.store');
});


Route::get('/login', fn () => redirect()->route('login.show'))->name('login');

/* Auth-only */
Route::middleware('auth')->group(function () {
    // IMPORTANT: logout route (POST)
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Customer dashboard + updates
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::put('/dashboard', [UserController::class, 'updateProfile'])->name('dashboard.update');
    Route::put('/dashboard/password', [UserController::class, 'updatePassword'])->name('dashboard.password');

        Route::middleware('auth')->group(function () {
    // admin page
    Route::get('/admin', function () {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return app(AdminDashboardController::class)->index();
    })->name('admin.page');
});
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
Route::post('/foods/{food}/reviews', [MenuController::class, 'storeOrUpdateMyReview'])->name('reviews.store');
Route::delete('/foods/{food}/reviews', [MenuController::class, 'destroyMyReview'])->name('reviews.destroy.mine');


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

Route::middleware('auth')->group(function () {
    Route::get('/payment/{order}', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment/{order}', [PaymentController::class, 'process'])->name('payment.process');
});


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

Route::get("stripe", [StripeController::class, "stripe"]);

// Admin routes - ensure you protect with proper middleware/gate in production
Route::middleware(['auth','can:refund-payments'])->group(function () {
    Route::get('/admin/payments', [PaymentWebController::class, 'adminHistory'])->name('admin.payments');
   // Route::get('/admin/payments/{id}/refund', [PaymentWebController::class, 'showRefundForm'])->name('admin.payments.refund.form');
    //Route::post('/admin/payments/{id}/refund', [PaymentWebController::class, 'postRefund'])->name('admin.payments.refund');
});

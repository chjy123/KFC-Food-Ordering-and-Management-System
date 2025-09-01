<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\CategoryController;   
use App\Http\Controllers\Admin\FoodController;    
use App\Http\Controllers\Admin\AdminReviewController; 
use App\Http\Controllers\Admin\AdminReportController;           

// Route::middleware(['auth','admin'])
//     ->prefix('admin')
//     ->name('admin.')
//     ->group(function () {
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


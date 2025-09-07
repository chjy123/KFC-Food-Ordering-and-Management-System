<?php
#author’s name： Pang Jun Meng
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentApiController;

Route::prefix('payments')
    ->middleware(['auth:sanctum', 'throttle:10,1'])
    ->group(function () {
        // create Stripe Checkout session (replaces old "process")
        Route::post('checkout', [PaymentApiController::class, 'checkout']);

        // read one payment
        Route::get('{id}', [PaymentApiController::class, 'show'])
            ->whereNumber('id');

        // user history
        Route::get('user/{id}', [PaymentApiController::class, 'history'])
            ->whereNumber('id');
    });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payments/checkout', [PaymentApiController::class, 'checkout']);
    Route::get('/payments/{id}',      [PaymentApiController::class, 'show']);
    Route::get('/payments/user/{id}', [PaymentApiController::class, 'listByUser']);
});

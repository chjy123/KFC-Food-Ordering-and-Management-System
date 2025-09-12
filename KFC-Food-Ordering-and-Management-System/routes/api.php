<?php
#author’s name： Pang Jun Meng
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FoodApiController;
use App\Http\Controllers\Api\UserWebServiceController;

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

Route::prefix('v1')->group(function () {
    Route::get('/foods', [FoodApiController::class, 'index'])->name('api.v1.foods.index');
    Route::get('/foods/{food}', [FoodApiController::class, 'show'])->name('api.v1.foods.show');
});

Route::get('/ping', function () {
    return response()->json(['ok' => true, 'where' => 'api.php']);
});

Route::prefix('v1')->group(function () {
    Route::get('/users/{userId}/info', [UserWebServiceController::class, 'getUserInfo'])
        ->name('api.v1.users.info');
});
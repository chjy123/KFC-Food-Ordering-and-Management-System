<?php
#author’s name： Pang Jun Meng
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FoodApiController;
use App\Http\Controllers\Api\UserWebServiceController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ReviewApiController;
use Illuminate\Http\Request;


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

#author’s name： Yew Kai Quan
Route::prefix('v1')->group(function () {
    Route::get('/foods', [FoodApiController::class, 'index'])->name('api.v1.foods.index');
    Route::get('/foods/{food}', [FoodApiController::class, 'show'])->name('api.v1.foods.show');
});

Route::get('/ping', function () {
    return response()->json(['ok' => true, 'where' => 'api.php']);
});

#author’s name： Yew Kai Quan
Route::prefix('v1')->group(function () {
    Route::get('/users/{userId}/info', [UserWebServiceController::class, 'getUserInfo'])
        ->name('api.v1.users.info');
});

#author’s name： Lim Jing Min
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Orders (read + advance status)
    Route::get('/orders', [OrderApiController::class, 'index']);                   // ?status=Received|Preparing|Completed
    Route::post('/orders/{order}/advance', [OrderApiController::class, 'advance']) // moves Received→Preparing→Completed
        ->whereNumber('order');

    // ✅ NEW: Order Web Services (exposure)
    Route::get('/orders/{order}', [OrderApiController::class, 'show'])             // read one order with items
        ->whereNumber('order');

    Route::post('/orders', [OrderApiController::class, 'store']);                  // create order from items[]
    // (store consumes Menu/Food API to validate price/availability)

    // Reviews (read + hard delete)
    Route::get('/reviews', [ReviewApiController::class, 'index']) ;                // ?rating=&q=
    Route::delete('/reviews/{review}', [ReviewApiController::class, 'destroy'])
        ->whereNumber('review');

        
});

#author’s name： Chow Jun Yu
Route::prefix('v1')->group(function () {
    Route::get('/foods', [FoodApiController::class, 'index']);
    Route::get('/foods/{food}', [FoodApiController::class, 'show']);
});

#author’s name： Lim Jun Hong
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'store']);
});
    
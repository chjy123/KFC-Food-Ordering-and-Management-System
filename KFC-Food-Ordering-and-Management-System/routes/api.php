<?php
#author’s name： Pang Jun Meng
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FoodApiController;

Route::prefix('payments')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::post('process', [PaymentController::class, 'process'])->middleware(['idempotency', 'verify.hmac']);
    Route::get('user/{userId}', [PaymentController::class, 'history']);
    Route::get('{id}', [PaymentController::class, 'show']);
    //Route::post('{id}/refund', [PaymentController::class, 'refund'])->middleware('can:refund-payments');
});

Route::prefix('v1')->group(function () {
    Route::get('/foods', [FoodApiController::class, 'index'])->name('api.v1.foods.index');
    Route::get('/foods/{food}', [FoodApiController::class, 'show'])->name('api.v1.foods.show');
});

Route::get('/ping', function () {
    return response()->json(['ok' => true, 'where' => 'api.php']);
});
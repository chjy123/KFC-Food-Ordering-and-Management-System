<?php
#author’s name： Pang Jun Meng
use App\Http\Controllers\Api\PaymentController;

Route::prefix('payments')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::post('process', [PaymentController::class, 'process'])->middleware(['idempotency', 'verify.hmac']);
    Route::get('user/{userId}', [PaymentController::class, 'history']);
    Route::get('{id}', [PaymentController::class, 'show']);
    //Route::post('{id}/refund', [PaymentController::class, 'refund'])->middleware('can:refund-payments');
});

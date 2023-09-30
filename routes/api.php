<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderProductController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->post('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
    Route::post('/orders/time', [OrderController::class, 'time']);
    Route::get('/orders/user/{id}', [OrderController::class, 'user']);
    Route::get('/orders/status/{status}', [OrderController::class, 'status']);
    Route::get('/orders/user/{id}/status/{status}', [OrderController::class, 'userStatus']);
    Route::get('/orders/user/top/{num}', [OrderController::class, 'topUser']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('order_products', OrderProductController::class);
    Route::get('/order_products/order/{id}', [OrderProductController::class, 'order']);
});

Route::get('/login', function () {
    return response()->json('Invalid credentials', 400);
})->name('login');

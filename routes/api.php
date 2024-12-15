<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MoneyTransferController;
use App\Http\Controllers\TransactionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/accounts', [UserController::class, 'getAccounts']);
    Route::get('/user/accounts/{account}/transactions', [UserController::class, 'getAccountTransactions']);
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::get('/user/accounts/{account}/transactions/search', [TransactionController::class, 'search']);
    Route::post('/user/accounts/{sender_account_id}/transfer', [MoneyTransferController::class, 'transfer']);
});

Route::get('/test', [TestController::class, 'test']);

Route::middleware('auth:sanctum')->get('/debug', function () {
    return response()->json(['user' => Auth::user()]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

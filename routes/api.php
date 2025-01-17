<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MoneyTransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\EnsureUserIsCorrectType;


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth:sanctum', EnsureUserIsCorrectType::class.':admin'])->group(function () {
    Route::resource('category', TransactionCategoryController::class)->only(['index', 'show']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('admin/users', [AdminController::class, 'showUsers']);
    Route::get('admin/accounts', [AdminController::class, 'showAccounts']);
    Route::put('admin/user/{user}', [AdminController::class, 'updateUser']);
    Route::post('admin/user', [AdminController::class, 'createUser']);
    Route::post('admin/category', [AdminController::class, 'createTransactionCategory']);
    Route::put('admin/category/{category}', [AdminController::class, 'updateTransactionCategory']);
    Route::delete('admin/category/{category}', [AdminController::class, 'deleteTransactionCategory']);
    Route::post('admin/account', [AdminController::class, 'createAccount']);
    Route::put('admin/account/{account}', [AdminController::class, 'updateAccount']);
    Route::delete('admin/account/{account}', [AdminController::class, 'deleteAccount']);
});

Route::middleware(['auth:sanctum', EnsureUserIsCorrectType::class.':user'])->group(function () {
    Route::get('/user/accounts', [UserController::class, 'getAccounts']);
    Route::get('/user/accounts/{account}/transactions', [UserController::class, 'getAccountTransactions']);
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::get('/user/accounts/{account}/transactions/search', [TransactionController::class, 'search']);
    Route::get('/user/transactions/{transaction}/print', [TransactionController::class, 'generatePDF']);
    Route::post('/user/accounts/{sender_account_id}/transfer', [MoneyTransferController::class, 'transfer']);
    Route::resource('currency', CurrencyController::class)->only(['index', 'show']);
    Route::get('/user/messages', [MessageController::class, 'showForUser']);
    Route::post('/user/messages', [MessageController::class, 'create']);
});

Route::middleware(['auth:sanctum', EnsureUserIsCorrectType::class.':support'])->group(function () {
    Route::resource('support/messages', MessageController::class)->only(['index', 'show']);
    Route::get('/support/messages/unsolved', [MessageController::class, 'showUnsolved']);
    Route::put('/support/messages/{message}/solve', [MessageController::class, 'markAsSolved']);
});

Route::get('/test', [TestController::class, 'test']);

Route::middleware('auth:sanctum')->get('/debug', function () {
    return response()->json(['user' => Auth::user()]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

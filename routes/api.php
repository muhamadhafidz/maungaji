<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register'] );
Route::post('/login', [AuthController::class, 'login'] );


Route::middleware(['jwt.verify'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'] );

    Route::get('/user', [AuthController::class, 'getUser'] );

    Route::put('/transaction/update/{id}', [TransactionController::class, 'updateTransaction'] );
    Route::delete('/transaction/delete/{id}', [TransactionController::class, 'deleteTransaction'] );
    Route::post('/transaction/create', [TransactionController::class, 'createTransaction'] );
    Route::get('/transaction', [TransactionController::class, 'getTransactionList'] );
    Route::get('/transaction/{id}', [TransactionController::class, 'getTransactionDetail'] );
});
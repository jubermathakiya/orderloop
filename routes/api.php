<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BorrowingController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//after login auth
Route::middleware(['token.expiry', 'auth:sanctum'])->group(function () {
    
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });

    Route::post('/borrow/{book}', [BorrowingController::class, 'borrow']);
    Route::post('/return/{book}', [BorrowingController::class, 'returnBook']);
    
    Route::post('logout', [AuthController::class, 'logout']);
});

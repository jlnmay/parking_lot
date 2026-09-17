<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'destroy']);
Route::get('/me', [AuthController::class, 'show']);

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');

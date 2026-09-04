<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AspirasiController;

Route::post('/aspirasi', [AspirasiController::class, 'store']);
Route::get('/aspirasi/{ticket}', [AspirasiController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

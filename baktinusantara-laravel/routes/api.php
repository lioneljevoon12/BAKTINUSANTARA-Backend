<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\AuthController; 

Route::post('/aspirasi', [AspirasiController::class, 'store']);
Route::get('/aspirasi/{ticket}', [AspirasiController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register/desa', [DesaController::class, 'register']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::patch('/admin/desa/{profilDesa}/verify', [DesaController::class, 'verify']);
});
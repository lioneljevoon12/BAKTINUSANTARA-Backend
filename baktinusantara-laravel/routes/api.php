<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\KelompokController;

Route::post('/aspirasi', [AspirasiController::class, 'store']);
Route::get('/aspirasi/{ticket}', [AspirasiController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/register/mahasiswa', [MahasiswaController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register/desa', [DesaController::class, 'register']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::patch('/admin/desa/{profilDesa}/verify', [DesaController::class, 'verify']);
    Route::patch('/admin/mahasiswa/{profilMahasiswa}/verify', [MahasiswaController::class, 'verify']);

});

Route::middleware(['auth:sanctum', 'role:perangkat_desa'])->group(function () {
    Route::get('/desa/aspirasi', [AspirasiController::class, 'indexByDesa']);
    Route::patch('/desa/aspirasi/{aspirasi}/decide', [AspirasiController::class, 'decide']);
});

Route::middleware(['auth:sanctum', 'role:mahasiswa'])->group(function () {
    Route::post('/kelompok', [KelompokController::class, 'store']);
    Route::post('/kelompok/{kelompok}/join', [KelompokController::class, 'join']);
    Route::get('/kelompok/{kelompok}', [KelompokController::class, 'show']);
});
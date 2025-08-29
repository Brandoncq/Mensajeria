<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm']);

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboardMonitor', function () {
    return view('dashboard.monitor');
})->middleware(['auth', 'role:monitor']);

Route::get('dashboardEditor', function () {
    return view('dashboard.editor');
})->middleware(['auth', 'role:editor']);

Route::get('dashboardAdministrador', function () {
    return view('dashboard.administrador');
})->middleware(['auth', 'role:administrador']);
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReporteAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Models\Reporte;
use App\Models\User;

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
    $reportes = Reporte::orderBy('fecha_sistema','desc')->get();
    $usuarios = User::orderBy('nombre', 'asc')->get();
    return view('dashboard.administrador', compact('reportes', 'usuarios'));
})->middleware(['auth', 'role:administrador']);

Route::middleware(['auth', 'role:monitor'])->group(function () {
    Route::get('monitor/reportar', [ReporteController::class, 'create'])->name('monitor.reportar');
    Route::post('monitor/reportar', [ReporteController::class, 'store']);
});

Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
    Route::get('reportes/{id}', [ReporteAdminController::class, 'show'])->name('reportes.show');
    Route::get('reportes/{id}/edit', [ReporteAdminController::class, 'edit'])->name('reportes.edit');
    Route::put('reportes/{id}', [ReporteAdminController::class, 'update'])->name('reportes.update');
    Route::delete('reportes/{id}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');

    Route::post('reportes/{id}/aprobar', [ReporteAdminController::class, 'aprobar'])->name('reportes.aprobar');
    Route::post('reportes/{id}/rechazar', [ReporteAdminController::class, 'rechazar'])->name('reportes.rechazar');

    Route::get('reportes/{id}/imprimir', [ReporteAdminController::class, 'imprimir'])->name('reportes.imprimir');
});



Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    // Usuarios
    Route::get('usuarios', [UserAdminController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/create', [UserAdminController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UserAdminController::class, 'store'])->name('usuarios.store');
    Route::get('usuarios/{id}', [UserAdminController::class, 'show'])->name('usuarios.show');
    Route::get('usuarios/{id}/edit', [UserAdminController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{id}', [UserAdminController::class, 'update'])->name('usuarios.update');
    Route::delete('usuarios/{id}', [UserAdminController::class, 'destroy'])->name('usuarios.destroy');
});
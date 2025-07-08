<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\AuditoriaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para docentes
Route::middleware(['role:docente', 'check.user.status'])->group(function () {
    Route::get('asignaturas', [AsignaturaController::class, 'index'])->name('asignaturas.index');
    Route::get('notas', [NotaController::class, 'index'])->name('notas.index');
    Route::post('notas', [NotaController::class, 'store'])->name('notas.store');
    Route::put('notas/{id}', [NotaController::class, 'update'])->name('notas.update');
    Route::post('notas/{id}/eliminar', [NotaController::class, 'destroy'])->name('notas.destroy');
    Route::post('notas/{id}/restaurar', [NotaController::class, 'restore'])->name('notas.restore');
    Route::post('notas/{id}/borrar-definitivo', [NotaController::class, 'forceDelete'])->name('notas.forceDelete');
});

// Rutas para estudiantes
Route::middleware(['role:estudiante', 'check.user.status'])->group(function () {
    Route::get('notas', [NotaController::class, 'index'])->name('notas.index');
});

// Rutas para admin
Route::middleware(['role:Administrador', 'check.user.status'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('users/{user}/inactivar', [UserController::class, 'inactivar'])->name('users.inactivar');
    Route::post('users/{user}/activar', [UserController::class, 'activar'])->name('users.activar');
    Route::get('auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');
});

require __DIR__.'/auth.php';

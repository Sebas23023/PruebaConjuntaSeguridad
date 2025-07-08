<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;

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

// Rutas para docentes (registrar y actualizar notas)
Route::middleware(['auth', 'role:docente'])->group(function () {
    Route::post('/notas', [NotaController::class, 'store'])->name('notas.store');
    Route::put('/notas/{id}', [NotaController::class, 'update'])->name('notas.update');
});

// Rutas para estudiantes (ver sus notas, ya está en el dashboard)
// Si necesitas una ruta API, puedes agregarla aquí

// Rutas para admin (acceso total, ejemplo)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Aquí puedes agregar rutas de administración
});

require __DIR__.'/auth.php';

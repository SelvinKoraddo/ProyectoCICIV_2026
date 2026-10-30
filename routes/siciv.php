<?php

use App\Http\Controllers\EntradaInventarioController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get(
        '/proveedores',
        [ProveedorController::class, 'index']
    )->name('proveedores.index');

    Route::post(
        '/proveedores',
        [ProveedorController::class, 'store']
    )->name('proveedores.store');

    Route::patch(
        '/proveedores/{proveedor}/estado',
        [ProveedorController::class, 'cambiarEstado']
    )->name('proveedores.estado');

    Route::get(
        '/entradas/create',
        [EntradaInventarioController::class, 'create']
    )->name('entradas.create');

    Route::post(
        '/entradas',
        [EntradaInventarioController::class, 'store']
    )->name('entradas.store');

});
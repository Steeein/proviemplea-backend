<?php

use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\PersonaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ProviEmplea
|--------------------------------------------------------------------------
|
| Todos los endpoints de la API REST de ProviEmplea.
| Base URL: http://localhost:8080/api
|
| Rate Limiting: 60 solicitudes por minuto por IP (configurado en AppServiceProvider).
|
*/

Route::middleware('throttle:api')->group(function () {

    // ── Health ────────────────────────────────────────────────────────────────
    Route::get('/health', HealthController::class);

    // ── Personas ──────────────────────────────────────────────────────────────
    // CRUD estándar generado por apiResource:
    //   GET    /personas          → index   (listar CV ciegos)
    //   POST   /personas          → store   (crear talento)
    //   GET    /personas/{id}     → show    (ver perfil completo — uso admin)
    //   PUT    /personas/{id}     → update  (actualizar perfil)
    //   DELETE /personas/{id}     → destroy (desactivar — soft delete)
    Route::apiResource('personas', PersonaController::class);

    // PATCH para encender/apagar visibilidad en vitrina (toggle validado)
    Route::patch('personas/{id}/validar', [PersonaController::class, 'validar'])
        ->name('personas.validar');

    // ── Empresas ──────────────────────────────────────────────────────────────
    Route::apiResource('empresas', EmpresaController::class);

    // PATCH para encender/apagar validación de empresa (toggle validado)
    Route::patch('empresas/{id}/validar', [EmpresaController::class, 'validar'])
        ->name('empresas.validar');

    // ── Administración ────────────────────────────────────────────────────────
    Route::prefix('admin')->group(function () {
        Route::get('contactos',                     [AdministracionController::class, 'listarContactos'])
            ->name('admin.contactos.index');
        Route::post('contactos',                    [AdministracionController::class, 'crearContacto'])
            ->name('admin.contactos.store');
        Route::patch('contactos/{id}/estado',       [AdministracionController::class, 'actualizarEstado'])
            ->name('admin.contactos.estado');
        Route::get('estadisticas',                  [AdministracionController::class, 'estadisticas'])
            ->name('admin.estadisticas');
    });
});

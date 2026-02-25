<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ERPController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas del ERP
Route::get('inicio', [CRMController::class, 'inicio'])->name('inicio');

// Vendedores
Route::get('altaVendedores', [CRMController::class, 'altaVendedores'])->name('altaVendedores');
Route::post('guardarVendedor', [CRMController::class, 'guardarVendedor'])->name('guardarVendedor');
Route::get('editarVendedor/{id}', [CRMController::class, 'editarVendedor'])->name('editarVendedor');
Route::post('actualizarVendedor/{id}', [CRMController::class, 'actualizarVendedor'])->name('actualizarVendedor');
Route::post('eliminarVendedor/{id}', [CRMController::class, 'eliminarVendedor'])->name('eliminarVendedor');

// Prospectos y Asignación
Route::get('altaProspectos', [CRMController::class, 'altaProspectos'])->name('altaProspectos');
Route::post('guardarProspecto', [CRMController::class, 'guardarProspecto'])->name('guardarProspecto');
Route::get('listarProspectos', [CRMController::class, 'listarProspectos'])->name('listarProspectos');
// Clientes y proyectos
Route::get('listarClientes', [CRMController::class, 'listarClientes'])->name('listarClientes');
Route::post('proyecto/guardar', [CRMController::class, 'guardarProyecto'])->name('guardarProyecto');
Route::get('clientes', [CRMController::class, 'clientes'])->name('clientes');
Route::get('crm/clientes/{id}/proyectos', [CRMController::class, 'obtenerProyectosCliente'])->name('obtenerProyectosCliente');
Route::get('crm/prospectos/{id}/proyectos', [CRMController::class, 'obtenerProyectosProspecto']);
Route::get('crm/prospecto/editar/{id}', [CRMController::class, 'editarProspecto'])->name('editarProspecto');
Route::put('crm/prospecto/actualizar/{id}', [CRMController::class, 'actualizarProspecto'])->name('actualizarProspecto');
Route::delete('crm/prospecto/eliminar/{id}', [CRMController::class, 'eliminarProspecto'])->name('eliminarProspecto');
Route::get('asignacionVendedor', [CRMController::class, 'asignacionVendedor'])->name('asignacionVendedor');
Route::post('asignacionVendedor', [CRMController::class, 'guardarAsignacion']);
Route::get('listarAsignaciones', [CRMController::class, 'listarAsignaciones'])->name('listarAsignaciones');
Route::get('reporteEstatus', [CRMController::class, 'reporteEstatus'])->name('reporteEstatus');
Route::post('crm/prospecto/venta-no-concluida/{id}', [CRMController::class, 'cambiarEstatusVentaNoConcluida'])->name('cambiarEstatusVentaNoConcluida');

// Artículos
Route::get('altaArticulos', [ERPController::class, 'altaArticulos'])->name('altaArticulos');
Route::post('guardarArticulo', [ERPController::class, 'guardarArticulo'])->name('guardarArticulo');
Route::get('gestionArticulos', [ERPController::class, 'gestionArticulos'])->name('gestionArticulos');
Route::get('altasCategorias', [ERPController::class, 'altasCategorias'])->name('altasCategorias');
Route::post('guardarCategoria', [ERPController::class, 'guardarCategoria'])->name('guardarCategoria');

// Reportes
Route::get('seguimientoProyectos', [ERPController::class, 'seguimientoProyectos'])->name('seguimientoProyectos');
Route::get('asignacionPrecios', [ERPController::class, 'asignacionPrecios'])->name('asignacionPrecios');

//logistica
Route::get('logistica', [ERPController::class, 'logistica'])->name('logistica');


// Otras rutas pueden ir aquí
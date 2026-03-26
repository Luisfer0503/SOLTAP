<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ERPController;

use App\Http\Controllers\AuthController;

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
Route::post('/erp/guardar-articulos-produccion', [App\Http\Controllers\ERPController::class, 'guardarArticulosProduccion']);
Route::get('/erp/detalle-proyecto/{id}', [App\Http\Controllers\ERPController::class, 'detalleProyecto'])->name('detalleProyecto');
Route::get('/erp/articulos-proyecto/{id}', [App\Http\Controllers\ERPController::class, 'obtenerArticulosProyecto']);

Route::get('altaArticulos', [ERPController::class, 'altaArticulos'])->name('altaArticulos');
Route::post('guardarArticulo', [ERPController::class, 'guardarArticulo'])->name('guardarArticulo');
Route::get('gestionArticulos', [ERPController::class, 'gestionArticulos'])->name('gestionArticulos');
Route::get('altasCategorias', [ERPController::class, 'altasCategorias'])->name('altasCategorias');
Route::post('guardarCategoria', [ERPController::class, 'guardarCategoria'])->name('guardarCategoria');
Route::post('/erp/guardar-nuevo-material', [ERPController::class, 'guardarNuevoMaterial'])->name('guardarNuevoMaterial');
Route::post('/erp/guardar-nueva-chapa', [ERPController::class, 'guardarNuevaChapa'])->name('guardarNuevaChapa');
Route::post('/erp/guardar-nuevo-proveedor', [ERPController::class, 'guardarNuevoProveedor'])->name('guardarNuevoProveedor');
Route::post('/erp/guardar-nuevo-submaterial', [ERPController::class, 'guardarNuevoSubmaterial'])->name('guardarNuevoSubmaterial');


// Rutas para el Módulo de Cobranza
Route::get('/erp/cobranza', [App\Http\Controllers\ERPController::class, 'cobranza'])->name('cobranza');
Route::get('/erp/plan-pagos/{cotizacion_id}', [App\Http\Controllers\ERPController::class, 'obtenerPlanPagos'])->name('obtenerPlanPagos');
Route::post('/erp/registrar-pago', [App\Http\Controllers\ERPController::class, 'registrarPago'])->name('registrarPago');


// Reportes
Route::get('seguimientoProyectos', [ERPController::class, 'seguimientoProyectos'])->name('seguimientoProyectos');
Route::get('asignacionPrecios', [ERPController::class, 'asignacionPrecios'])->name('asignacionPrecios');
Route::post('generarCotizacionPdf', [ERPController::class, 'generarCotizacionPdf'])->name('generarCotizacionPdf');
Route::post('generarRemisionPdf', [ERPController::class, 'generarRemisionPdf'])->name('generarRemisionPdf');
Route::get('/erp/obtener-cotizacion/{id}', [App\Http\Controllers\ERPController::class, 'obtenerCotizacion']);
Route::post('guardarCotizacion', [ERPController::class, 'guardarCotizacion'])->name('guardarCotizacion');
Route::post('/erp/autorizar-cotizacion-interna', [App\Http\Controllers\ERPController::class, 'autorizarCotizacionInterna'])->name('autorizarCotizacionInterna');
Route::post('/erp/ajustar-cotizacion-interna', [App\Http\Controllers\ERPController::class, 'ajustarCotizacionInterna'])->name('ajustarCotizacionInterna');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// Rutas de Recuperación de Contraseña
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('guest')->name('password.update');

Route::get('/users/{user}/edit', [AuthController::class, 'editUser'])->name('users.edit');
Route::put('/users/{user}', [AuthController::class, 'updateUser'])->name('users.update');
Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');

// En tu archivo routes/web.php

Route::get('/users/{user}/edit', [AuthController::class, 'editUser'])->name('users.edit');
Route::put('/users/{user}', [AuthController::class, 'updateUser'])->name('users.update');
Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');


// Rutas de Administración de Usuarios
Route::get('/usuarios', [AuthController::class, 'indexUsers'])->name('users.index');
Route::post('/usuarios', [AuthController::class, 'storeUser'])->name('users.store');

//logistica
Route::get('logistica', [ERPController::class, 'logistica'])->name('logistica');
Route::post('/erp/guardar-logistica-proyecto', [ERPController::class, 'guardarLogisticaProyecto'])->name('guardarLogisticaProyecto');


// Otras rutas pueden ir aquí
Route::post('/generarProduccionPdf', [ERPController::class, 'generarProduccionPdf'])->name('generarProduccionPdf');
Route::get('/vistaProduccionProyecto/{id}', [ERPController::class, 'vistaProduccionProyecto'])->name('vistaProduccionProyecto');
Route::post('/guardarInteraccionProduccion', [ERPController::class, 'guardarInteraccionProduccion'])->name('guardarInteraccionProduccion');
Route::get('/escaner-produccion', [ERPController::class, 'escanerProduccion'])->name('escanerProduccion');
Route::get('/reporteEstatusProyecto', [ERPController::class, 'reporteEstatusProyecto'])->name('reporteEstatusProyecto');
Route::get('/erp/proyecto-interacciones/{id}', [ERPController::class, 'obtenerHistorialInteracciones']);

Route::get('/fallas', [ERPController::class, 'fallas'])->name('fallas');    
Route::post('erp/guardarFalla', [ERPController::class, 'guardarFalla'])->name('guardarFalla');
Route::get('/falla/{id}', [ERPController::class, 'detalleFalla'])->name('detalleFalla');
Route::post('/falla/{id}/actualizar', [ERPController::class, 'actualizarFalla'])->name('actualizarFalla');
Route::post('/falla/{id}/eliminar', [ERPController::class, 'eliminarFalla'])->name('eliminarFalla');    
Route::get('/erp/articulos/{id}/fallas', [ERPController::class, 'obtenerFallasArticulo'])->name('obtenerFallasArticulo');
Route::get('/erp/alta-estatus', [App\Http\Controllers\ERPController::class, 'altaEstatus'])->name('altaEstatus');
Route::post('/erp/guardar-alta-estatus', [App\Http\Controllers\ERPController::class, 'guardarAltaEstatus'])->name('guardarAltaEstatus');

Route::post('/erp/logistica/retorno', [App\Http\Controllers\ERPController::class, 'guardarRetorno'])->name('guardarRetorno');
Route::post('/erp/guardar-verificacion-articulos', [App\Http\Controllers\ERPController::class, 'guardarVerificacionArticulos']);
Route::get('/erp/proyecto-historial-pdf/{id}', [ERPController::class, 'imprimirHistorialProyecto'])->name('proyecto.historial.pdf');

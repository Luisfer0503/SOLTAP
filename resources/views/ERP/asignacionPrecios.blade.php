@extends('principal')

@section('contenido')

    @php
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $puedeAutorizar = in_array($userRoleName, ['ADMIN', 'COORD. DV&MKT', 'COORD. DV SOLFERINO', 'VENDEDOR/DISEÑADOR']);
        $puedeDesbloquear = in_array(strtoupper($userRoleName), ['ADMIN', 'DIRECCIÓN', 'DIRECCION']);
    @endphp

    <style>
        /* Ocultar flechas (spinners) en inputs numéricos */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="cotizadorApp()">
    <!-- Header -->
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-currency-dollar text-green-600 mr-2"></i> Asignación de Precios y Cotizaciones
            </h2>
        </div>
        <div class="relative w-full max-w-md">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="filtro" placeholder="Buscar por proyecto o cliente..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
    </header>

    <!-- Listado de Proyectos -->
    <div class="flex-1 overflow-y-auto p-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto cursor-grab active:cursor-grabbing"
                 x-data="{ isDown: false, startX: 0, scrollLeft: 0 }"
                 :class="{ 'select-none': isDown }"
                 @mousedown="isDown = true; startX = $event.pageX - $el.offsetLeft; scrollLeft = $el.scrollLeft"
                 @mouseleave="isDown = false"
                 @mouseup="isDown = false"
                 @mousemove="if(!isDown) return; $event.preventDefault(); const x = $event.pageX - $el.offsetLeft; const walk = (x - startX) * 1.5; $el.scrollLeft = scrollLeft - walk;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Vendedor</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="p in proyectosFiltrados" :key="p.proyecto_id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900" x-text="p.nombre_proyecto"></div>
                                <div class="text-xs text-gray-500" x-text="formatDate(p.fecha)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" x-text="p.cliente_nombre"></div>
                                <div class="text-xs text-gray-500" x-text="p.direccion"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" x-text="p.telefono"></div>
                                <div class="text-xs text-gray-500" x-text="p.correo"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="p.vendedor_nombre"></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <template x-if="p.tiene_pagos > 0">
                                        <div class="flex gap-2">
                                            <button @click="abrirCotizador(p)" class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded-md font-bold hover:bg-gray-600 transition shadow-sm flex items-center" title="Cotización bloqueada por pagos existentes">
                                                <i class="ph ph-lock-key mr-1"></i> Ver Cotización
                                            </button>
                                            @if($puedeDesbloquear)
                                            <button @click="desbloquearProyecto(p)" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-md font-bold hover:bg-red-700 transition shadow-sm flex items-center" title="Eliminar plan de pagos y desbloquear proyecto">
                                                <i class="ph ph-lock-open mr-1"></i> Desbloquear
                                            </button>
                                            @endif
                                        </div>
                                    </template>
                                    <template x-if="p.tiene_pagos == 0">
                                        <button @click="abrirCotizador(p)" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md font-bold hover:bg-green-700 transition shadow-sm flex items-center" title="Cotizar">
                                            <i class="ph ph-calculator mr-1"></i> Cotizar
                                        </button>
                                    </template>
                                    
                                    <button @click="abrirModalPagos(p)" 
                                            class="px-3 py-1.5 text-sm bg-gray-700 text-white rounded-md font-bold hover:bg-gray-800 transition shadow-sm flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            :title="(p.tiene_cotizacion && p.articulos_pendientes == 0) ? 'Generar Remisión' : 'Complete la cotización primero (Precios y Totales)'"
                                            :disabled="!p.tiene_cotizacion || p.articulos_pendientes > 0">
                                        <i class="ph ph-file-text mr-1"></i> Remisión
                                    </button>
                                    
                                    <button @click="abrirModalProduccion(p)" 
                                            class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 transition shadow-sm flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            title="Generar PDF Producción (Selección)"
                                            :disabled="!p.tiene_cotizacion || p.articulos_pendientes > 0">
                                        <i class="ph ph-factory mr-1"></i> Producción
                                    </button>
                                    
                                    <button @click="abrirModalActa(p)" 
                                            class="px-3 py-1.5 text-sm bg-teal-600 text-white rounded-md font-bold hover:bg-teal-700 transition shadow-sm flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            title="Generar Acta de Entrega"
                                            :disabled="!p.tiene_cotizacion || p.articulos_pendientes > 0">
                                        <i class="ph ph-clipboard-text mr-1"></i> Acta
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="proyectosFiltrados.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron proyectos que coincidan con la búsqueda.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Modal Cotizador -->
    <div x-show="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white w-full max-w-6xl h-[90vh] rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-bold flex items-center"><i class="ph ph-file-text mr-2"></i> Cotización de Proyecto</h3>
                    <p class="text-sm text-gray-300" x-text="proyecto?.nombre_proyecto"></p>
                </div>
                <button @click="cerrarModal()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <div x-show="cotizacionBloqueada" class="mb-6 p-4 bg-yellow-100 text-yellow-800 rounded-lg border border-yellow-200 text-sm font-bold flex items-center shadow-sm">
                    <i class="ph ph-lock-key mr-2 text-xl"></i> 
                    <span>La cotización está bloqueada porque existen pagos en proceso. No se pueden modificar precios ni totales.</span>
                </div>

                <!-- Info Proyecto -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 grid grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Cliente</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.cliente_nombre"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Teléfono</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.telefono"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Correo</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.correo"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Fecha</span>
                        <span class="font-semibold text-gray-800" x-text="formatDate(proyecto?.fecha)"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500 text-xs font-bold uppercase">Dirección</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.direccion"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500 text-xs font-bold uppercase">Vendedor</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.vendedor_nombre"></span>
                    </div>
                </div>

                <!-- Tabla Artículos -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Cant.</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Dimensiones / Peso</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Precio Unit.</th>
                                <th x-show="isSHProject" class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Adicional Unit.</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in articulos" :key="index">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 flex-shrink-0 bg-gray-100 rounded overflow-hidden mr-3 border border-gray-200">
                                                <template x-if="item.imagen">
                                                    <img :src="item.imagen" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!item.imagen">
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400"><i class="ph ph-image"></i></div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900" x-text="item.nombre"></div>
                                                <div class="text-xs text-gray-500" x-text="item.id_articulo_produccion"></div>
                                                <div class="text-xs text-gray-400 truncate w-48" x-text="item.descripcion"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-800" x-text="item.cantidad"></td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-600">
                                        <div x-text="`${item.alto}x${item.ancho}x${item.profundo}m`"></div>
                                        <div x-text="`${item.peso}kg`"></div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number" x-model="item.precio_unitario" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-24 text-right text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500" placeholder="0.00">
                                    </td>
                                    <td x-show="isSHProject" class="px-4 py-3 text-right">
                                        <input type="number" x-model="item.adicional_unitario" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-24 text-right text-sm border-gray-300 rounded py-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500" placeholder="0.00">
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900" x-text="money(item.cantidad * ((parseFloat(item.precio_unitario) || 0) + (parseFloat(item.adicional_unitario) || 0)))"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>

                <!-- Nuevos campos -->
                <div x-show="isSHProject" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" style="display: none;">
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tiempo de Entrega</label>
                        <input type="text" x-model="tiempo_entrega" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-full text-sm border-gray-300 rounded disabled:bg-gray-100" placeholder="Ej: 10 a 12 semanas...">
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Entrega (Condiciones de acceso)</label>
                        <input type="text" x-model="proyecto.condiciones_acceso" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-full text-sm border-gray-300 rounded disabled:bg-gray-100" placeholder="Ej: Sin condiciones especiales...">
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Observaciones</label>
                        <textarea x-model="observaciones" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" rows="2" class="w-full text-sm border-gray-300 rounded disabled:bg-gray-100" placeholder="Ej: Al aceptar esta cotización..."></textarea>
                    </div>
                </div>

                <!-- Totales -->
                <div class="flex justify-end">
                    <div class="w-80 bg-white p-4 rounded-lg shadow-sm border border-gray-200 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal Artículos:</span>
                            <span class="font-bold text-gray-900" x-text="money(subtotalArticulos)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Envío:</span>
                            <input type="number" x-model="costoEnvio" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-24 text-right text-sm border-gray-300 rounded py-1 disabled:bg-gray-100 disabled:text-gray-500" placeholder="0">
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Instalación:</span>
                            <input type="number" x-model="costoInstalacion" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-24 text-right text-sm border-gray-300 rounded py-1 disabled:bg-gray-100 disabled:text-gray-500" placeholder="0">
                        </div>
                        
                        <!-- Botón para editar configuración -->
                        <div class="flex justify-end mt-1">
                            <button @click="editarConfiguracion = !editarConfiguracion" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="text-xs text-blue-600 hover:text-blue-800 underline flex items-center disabled:text-gray-400 disabled:no-underline disabled:cursor-not-allowed">
                                <i class="ph" :class="editarConfiguracion ? 'ph-lock-key' : 'ph-pencil-simple'"></i>
                                <span class="ml-1" x-text="editarConfiguracion ? 'Bloquear Configuración' : 'Editar IVA/Descuento'"></span>
                            </button>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center">
                                <span class="text-gray-600">Descuento</span>
                                <div x-show="editarConfiguracion || descuentoPorcentaje > 0" class="ml-1 flex items-center">
                                    <span x-show="!editarConfiguracion" x-text="'(' + descuentoPorcentaje + '%)'" class="text-xs text-gray-500"></span>
                                    <div x-show="editarConfiguracion" class="flex items-center">
                                        <input type="number" x-model="descuentoPorcentaje" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-12 text-right text-xs border-gray-300 rounded py-0.5 px-1 focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                                        <span class="text-xs text-gray-500 ml-0.5">%</span>
                                    </div>
                                </div>
                                <span class="text-gray-600 ml-0.5">:</span>
                            </div>
                            <template x-if="descuentoPorcentaje > 0">
                                <span class="font-bold text-gray-900" x-text="money(descuentoCalculado)"></span>
                            </template>
                            <template x-if="descuentoPorcentaje == 0">
                                <input type="number" x-model="descuento" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-24 text-right text-sm border-gray-300 rounded py-1 disabled:bg-gray-100 disabled:text-gray-500" placeholder="0">
                            </template>
                        </div>
                        <div class="border-t border-gray-100 pt-2 flex justify-between text-sm">
                            <span class="text-gray-600 font-bold">Subtotal:</span>
                            <span class="font-bold text-gray-900" x-text="money(subtotalGeneral)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center">
                                <span class="text-gray-600">IVA</span>
                                <div class="ml-1 flex items-center">
                                    <span x-show="!editarConfiguracion" x-text="'(' + ivaPorcentaje + '%)'" class="text-xs text-gray-500"></span>
                                    <div x-show="editarConfiguracion" class="flex items-center">
                                        <input type="number" x-model="ivaPorcentaje" @input="cotizacionAutorizada = false" :disabled="cotizacionBloqueada || cotizacionAutorizada" class="w-12 text-right text-xs border-gray-300 rounded py-0.5 px-1 focus:ring-blue-500 focus:border-blue-500">
                                        <span class="text-xs text-gray-500 ml-0.5">%</span>
                                    </div>
                                </div>
                                <span class="text-gray-600 ml-0.5">:</span>
                            </div>
                            <span class="font-bold text-gray-900" x-text="money(iva)"></span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg">
                            <span class="font-bold text-gray-800">Total a Pagar:</span>
                            <span class="font-bold text-blue-600" x-text="money(totalPagar)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                <button @click="cerrarModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                @if($puedeAutorizar)
                <button @click="autorizarCotizacion()" x-show="!cotizacionAutorizada" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-sm flex items-center" :disabled="autorizando">
                    <i class="ph ph-check-circle mr-2" x-show="!autorizando"></i>
                    <i class="ph ph-spinner animate-spin mr-2" x-show="autorizando"></i>
                    <span x-text="autorizando ? 'Autorizando...' : 'Autorizar'"></span>
                </button>
                <div x-show="cotizacionAutorizada" class="px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-lg font-bold flex items-center shadow-sm">
                    <i class="ph ph-shield-check mr-2 text-lg"></i> Autorizada
                </div>
                <button @click="ajustarCotizacion()" x-show="cotizacionAutorizada && !cotizacionBloqueada" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition shadow-sm flex items-center" :disabled="ajustando">
                    <i class="ph ph-pencil-simple mr-2" x-show="!ajustando"></i>
                    <i class="ph ph-spinner animate-spin mr-2" x-show="ajustando"></i>
                    <span x-text="ajustando ? 'Desbloqueando...' : 'Ajustar'"></span>
                </button>
                @endif
                <button @click="guardarCotizacion()" x-show="!cotizacionBloqueada && !cotizacionAutorizada" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
                    <i class="ph ph-floppy-disk mr-2"></i> Guardar
                </button>
                <button @click="imprimirPdf()" 
                        :disabled="!esCotizacionValida || !cotizacionAutorizada"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                        :title="!cotizacionAutorizada ? 'Debe autorizar internamente la cotización para habilitar el PDF' : (esCotizacionValida ? 'Generar y descargar PDF' : 'Complete todos los precios, envío e instalación para habilitar')">
                    <i class="ph ph-file-pdf mr-2"></i> Generar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Configuración de Pagos (Remisión) -->
    <div x-show="mostrarModalPagos" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;" x-transition>
        <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="ph ph-credit-card mr-2"></i> Plan de Pagos - Remisión</h3>
                <button @click="mostrarModalPagos = false" class="text-gray-400 hover:text-white text-2xl">&times;</button>
            </div>
            
            <div class="p-6 bg-gray-50 flex-1 overflow-y-auto">
                <div class="mb-4 flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded border border-gray-200 gap-4">
                    <div class="flex flex-wrap gap-8 w-full md:w-auto">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold" x-text="isSHProjectPagos ? 'Total a Pagar (Cliente)' : 'Total a Pagar'"></p>
                            <p class="text-2xl font-bold text-gray-800" x-text="totalCotizacionOriginal ? money(totalCotizacionOriginal) : '---'"></p>
                        </div>
                        <div x-show="isSHProjectPagos" x-cloak>
                            <p class="text-xs text-gray-500 uppercase font-bold">Total Pagar Tapier</p>
                            <p class="text-2xl font-bold text-blue-600" x-text="money(articulosPagos.reduce((sum, art) => sum + (parseFloat(art.pago_tapier_monto) || 0), 0) + (parseFloat(instalacionTapierMonto) || 0))"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                        <label class="text-sm font-bold text-gray-700">Cantidad de Pagos:</label>
                        <input type="number" x-model="numeroPagos" @change="generarPagos()" min="1" max="10" class="w-20 text-center border-gray-300 rounded font-bold" :disabled="planBloqueado || (isSHProjectPagos ? (articulosPagos.reduce((sum, art) => sum + (parseFloat(art.pago_tapier_monto) || 0), 0) <= 0) : totalRemision === null)">
                    </div>
                </div>

                <div class="mb-4 bg-white p-4 rounded border border-gray-200">
                    <label for="rfc_remision" class="block text-sm font-bold text-gray-700">RFC (Opcional)</label>
                    <input type="text" id="rfc_remision" x-model="rfcRemision" placeholder="Ingrese el RFC del cliente" class="mt-1 w-full text-sm border-gray-300 rounded-lg uppercase focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4 bg-white p-4 rounded border border-gray-200">
                    <label for="condiciones_remision" class="block text-sm font-bold text-gray-700">Condiciones de Pago (Opcional)</label>
                    <textarea id="condiciones_remision" x-model="condicionesRemision" placeholder="Ej. Pago a 30 días, 50% anticipo..." class="mt-1 w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" rows="2"></textarea>
                </div>

                <!-- Desglose de Pago Tapier -->
                <div class="mb-8 border-b border-gray-200 pb-6" x-show="isSHProjectPagos" x-cloak>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-bold text-gray-800"><i class="ph ph-list-numbers mr-2"></i> Desglose de Pago Tapier por Artículo</h4>
                        <div class="flex items-center gap-2 bg-white p-2 rounded border border-gray-200 shadow-sm">
                            <label class="text-sm font-bold text-gray-700">Aplicar a todos:</label>
                            <input type="number" x-model="porcentajeGlobalTapier" min="0" max="100" class="w-16 text-center text-sm border-gray-300 rounded py-1 focus:ring-blue-500 focus:border-blue-500" placeholder="%">
                            <span class="text-gray-500 font-bold">%</span>
                            <button @click="aplicarPorcentajeGlobalTapier()" class="px-3 py-1 bg-blue-600 text-white rounded text-xs font-bold hover:bg-blue-700 transition shadow-sm">Aplicar</button>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Imagen</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Precio Total</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">% Pago Tapier</th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Monto Pago Tapier</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(art, index) in articulosPagos" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded overflow-hidden border border-gray-200 mx-auto">
                                                <template x-if="art.imagen">
                                                    <img :src="art.imagen" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!art.imagen">
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400"><i class="ph ph-image"></i></div>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-sm font-bold text-gray-800">
                                            <span x-text="art.nombre"></span>
                                            <div class="text-xs text-gray-500 font-normal" x-text="`Cant: ${art.cantidad}`"></div>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm text-gray-900 font-bold" x-text="money(art.total_calculado)"></td>
                                        <td class="px-4 py-2 text-center">
                                            <div class="flex items-center justify-center">
                                                <input type="number" x-model="art.pago_tapier_porcentaje" @input="calcularMontoTapierArticulo(art)" class="w-16 text-center text-sm border-gray-300 rounded py-1 focus:ring-blue-500 focus:border-blue-500">
                                                <span class="ml-1 text-gray-500">%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-right text-sm font-bold text-blue-600" x-text="money(art.pago_tapier_monto)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr x-show="instalacionTotal > 0">
                                    <td colspan="2" class="px-4 py-2 text-sm font-bold text-gray-800 text-right">Instalación</td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-900 font-bold" x-text="money(instalacionTotal)"></td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center">
                                            <input type="number" x-model="instalacionTapierPorcentaje" @input="calcularMontoTapierInstalacion()" class="w-16 text-center text-sm border-gray-300 rounded py-1 focus:ring-blue-500 focus:border-blue-500">
                                            <span class="ml-1 text-gray-500">%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-bold text-blue-600" x-text="money(instalacionTapierMonto)"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-700">Total Pago Tapier:</td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-blue-700" x-text="money(articulosPagos.reduce((sum, art) => sum + (parseFloat(art.pago_tapier_monto) || 0), 0) + (parseFloat(instalacionTapierMonto) || 0))"></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right border-t border-gray-200">
                                        <button @click="guardarTotalTapier()" class="px-4 py-2 bg-green-600 text-white rounded font-bold text-sm shadow hover:bg-green-700 transition">
                                            <i class="ph ph-floppy-disk mr-1"></i> Guardar Monto para Plan de Pagos
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>

                <h4 class="text-md font-bold text-gray-800 mb-4"><i class="ph ph-calendar mr-2"></i> Calendario de Pagos</h4>

                <div x-show="planBloqueado" class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded border border-yellow-200 text-sm font-bold flex items-center">
                    <i class="ph ph-lock-key mr-2 text-lg"></i> El plan de pagos está bloqueado porque ya existen abonos registrados.
                </div>

                <div class="bg-white rounded border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Concepto</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">% Porcentaje</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(pago, index) in listaPagos" :key="index">
                                <tr>
                                    <td class="px-4 py-2 text-sm font-bold text-gray-800" x-text="pago.nombre"></td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center">
                                            <input type="number" x-model="pago.porcentaje" @input="calcularMontos()" class="w-16 text-center text-sm border-gray-300 rounded py-1 focus:ring-blue-500 focus:border-blue-500" :disabled="planBloqueado">
                                            <span class="ml-1 text-gray-500">%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm font-bold text-gray-900" x-text="money(pago.monto)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
                
                <div class="mt-2 text-right">
                    <p class="text-xs font-bold" :class="sumaPorcentajes == 100 ? 'text-green-600' : 'text-red-600'">
                        Suma: <span x-text="sumaPorcentajes.toFixed(2)"></span>%
                    </p>
                </div>
            </div>

            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button @click="mostrarModalPagos = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                <button x-show="isSHProjectPagos" @click="generarRemisionConPagos('tapier')" class="px-6 py-2 bg-blue-800 text-white rounded-lg font-bold hover:bg-blue-900 transition shadow-lg flex items-center">
                    <i class="ph ph-file-pdf mr-2"></i> Remisión Tapier
                </button>
                <button @click="generarRemisionConPagos('solferino')" class="px-6 py-2 bg-gray-800 text-white rounded-lg font-bold hover:bg-gray-900 transition shadow-lg flex items-center">
                    <i class="ph ph-file-pdf mr-2"></i> <span x-text="isSHProjectPagos ? 'Remisión Solferino' : 'Generar Remisión'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Selección Artículos Producción -->
    <div x-show="mostrarModalProduccion" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;" x-transition>
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <div class="bg-indigo-700 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold flex items-center"><i class="ph ph-factory mr-2"></i> Orden de Producción - Seleccionar Artículos</h3>
                <button @click="mostrarModalProduccion = false" class="text-indigo-200 hover:text-white text-2xl">&times;</button>
            </div>
            
            <div class="p-6 bg-gray-50 overflow-y-auto max-h-[70vh]">
                <p class="text-sm text-gray-600 mb-4">Seleccione los artículos que desea enviar a la orden de producción. Los artículos que ya han sido enviados previamente aparecerán bloqueados; si desea volver a imprimirlos, use la opción de desbloquear.</p>
                
                <div class="mb-4 bg-white p-4 rounded border border-gray-200">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tiempo de Entrega (Manual)</label>
                    <input type="text" x-model="tiempoEntregaProduccion" placeholder="Ej. 4 a 5 semanas" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="bg-white rounded border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">Select</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Cant.</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in articulosProduccion" :key="index">
                                <tr :class="{'bg-gray-50': item.impreso && !item.desbloqueado}">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" x-model="item.seleccionado" :disabled="item.impreso && !item.desbloqueado" class="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed w-4 h-4">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-bold text-gray-900" x-text="item.nombre"></div>
                                        <div class="text-xs text-gray-500" x-text="item.id_articulo_produccion || 'Sin código'"></div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-800" x-text="item.cantidad"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span x-show="item.impreso" class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800"><i class="ph ph-check-circle mr-1"></i> Impreso</span>
                                        <span x-show="!item.impreso" class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800"><i class="ph ph-clock mr-1"></i> Pendiente</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button x-show="item.impreso" @click="toggleDesbloqueo(item)" class="text-xs font-bold px-3 py-1 rounded transition shadow-sm" :class="item.desbloqueado ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                                            <i class="ph" :class="item.desbloqueado ? 'ph-lock-open' : 'ph-lock-key'"></i>
                                            <span x-text="item.desbloqueado ? 'Bloquear' : 'Desbloquear'"></span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="articulosProduccion.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm italic">Cargando artículos o el proyecto no tiene artículos registrados...</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button @click="mostrarModalProduccion = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                <button @click="imprimirSeleccionadosPdf()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg flex items-center disabled:opacity-50" :disabled="generandoPdf || articulosProduccion.filter(a => a.seleccionado).length === 0">
                    <i class="ph ph-printer mr-2" x-show="!generandoPdf"></i>
                    <i class="ph ph-spinner animate-spin mr-2" x-show="generandoPdf"></i>
                    <span x-text="generandoPdf ? 'Generando PDF...' : 'Generar PDF (' + articulosProduccion.filter(a => a.seleccionado).length + ')'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Selección Artículos Acta de Entrega -->
    <div x-show="mostrarModalActa" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;" x-transition>
        <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <div class="bg-teal-700 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold flex items-center"><i class="ph ph-clipboard-text mr-2"></i> Acta de Entrega - Seleccionar Artículos</h3>
                <button @click="mostrarModalActa = false" class="text-teal-200 hover:text-white text-2xl">&times;</button>
            </div>
            
            <div class="p-6 bg-gray-50 overflow-y-auto max-h-[70vh]">
                <p class="text-sm text-gray-600 mb-4">Seleccione los artículos que desea incluir en el Acta de Entrega.</p>
                
                <div class="mb-4 bg-white p-4 rounded border border-gray-200">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Partida</label>
                    <input type="text" x-model="partidaActa" placeholder="Ej. Partida 1" class="w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="bg-white rounded border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">
                                    <input type="checkbox" x-model="seleccionarTodosActa" @change="toggleSeleccionarTodosActa()" class="rounded text-teal-600 focus:ring-teal-500 cursor-pointer w-4 h-4">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Cant.</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Comentarios</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in articulosActa" :key="index">
                                <tr :class="{'bg-gray-50': item.impreso && !item.desbloqueado}">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" x-model="item.seleccionado" :disabled="item.impreso && !item.desbloqueado" class="rounded text-teal-600 focus:ring-teal-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed w-4 h-4">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-bold text-gray-900" x-text="item.nombre"></div>
                                        <div class="text-xs text-gray-500" x-text="item.id_articulo_produccion || 'Sin código'"></div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-800" x-text="item.cantidad"></td>
                                    <td class="px-4 py-3">
                                        <input type="text" x-model="item.comentarios_acta" :disabled="!item.seleccionado" class="w-full text-sm border-gray-300 rounded focus:ring-teal-500 focus:border-teal-500 disabled:bg-gray-100" placeholder="Comentarios...">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span x-show="item.impreso" class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800"><i class="ph ph-check-circle mr-1"></i> Impreso</span>
                                        <span x-show="!item.impreso" class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800"><i class="ph ph-clock mr-1"></i> Pendiente</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button x-show="item.impreso" @click="toggleDesbloqueoActa(item)" class="text-xs font-bold px-3 py-1 rounded transition shadow-sm" :class="item.desbloqueado ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                                            <i class="ph" :class="item.desbloqueado ? 'ph-lock-open' : 'ph-lock-key'"></i>
                                            <span x-text="item.desbloqueado ? 'Bloquear' : 'Desbloquear'"></span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="articulosActa.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm italic">Cargando artículos...</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button @click="mostrarModalActa = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                <button @click="imprimirActaSeleccionadosPdf()" class="px-6 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition shadow-lg flex items-center disabled:opacity-50" :disabled="generandoActaPdf || articulosActa.filter(a => a.seleccionado).length === 0">
                    <i class="ph ph-printer mr-2" x-show="!generandoActaPdf"></i>
                    <i class="ph ph-spinner animate-spin mr-2" x-show="generandoActaPdf"></i>
                    <span x-text="generandoActaPdf ? 'Generando PDF...' : 'Generar PDF (' + articulosActa.filter(a => a.seleccionado).length + ')'"></span>
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    function cotizadorApp() {
        return {
            proyectos: @json($proyectos),
            filtro: '',
            mostrarModal: false,
            proyecto: null,
            articulos: [],
            costoEnvio: 0,
            costoInstalacion: 0,
            descuento: 0,
            ivaPorcentaje: 16,
            descuentoPorcentaje: 0,
            editarConfiguracion: false,
            cotizacionBloqueada: false,
            cotizacionAutorizada: false,
            cotizacionActualId: null,
            tiempo_entrega: '',
            observaciones: '',
            
            // Variables para Modal Pagos
            mostrarModalPagos: false,
            totalRemision: null,
            totalCotizacionOriginal: 0,
            numeroPagos: 2,
            listaPagos: [],
            proyectoPagos: null, // {id, nombre}
            planBloqueado: false,
            rfcRemision: '',
            condicionesRemision: '',
            autorizando: false,
            ajustando: false,
            articulosPagos: [],
            porcentajeGlobalTapier: 0,
            
            instalacionTotal: 0,
            instalacionTapierPorcentaje: 0,
            instalacionTapierMonto: 0,
            
            // Variables Producción
            mostrarModalProduccion: false,
            proyectoProduccion: null,
            articulosProduccion: [],
            tiempoEntregaProduccion: '',
            generandoPdf: false,
            generandoActaPdf: false,

            // Variables Acta de Entrega
            mostrarModalActa: false,
            proyectoActa: null,
            articulosActa: [],
            seleccionarTodosActa: true,
            partidaActa: '',

            get proyectosFiltrados() {
                if (this.filtro === '') {
                    return this.proyectos;
                }
                const busqueda = this.filtro.toLowerCase();
                return this.proyectos.filter(p => {
                    const nombreProyecto = p.nombre_proyecto ? p.nombre_proyecto.toLowerCase() : '';
                    const nombreCliente = p.cliente_nombre ? p.cliente_nombre.toLowerCase() : '';
                    return nombreProyecto.includes(busqueda) || nombreCliente.includes(busqueda);
                });
            },

            get isSHProject() {
                if (!this.proyecto || !this.proyecto.nombre_proyecto) return false;
                return this.proyecto.nombre_proyecto.toUpperCase().startsWith('SH-');
            },
            
            get isSHProjectPagos() {
                return this.proyectoPagos && this.proyectoPagos.nombre.toUpperCase().startsWith('SH-');
            },

            async abrirCotizador(proyecto) {
                this.proyecto = proyecto;
                this.articulos = [];
                this.costoEnvio = ''; // Inicializar vacío para obligar validación
                this.costoInstalacion = ''; // Inicializar vacío para obligar validación
                this.rfcRemision = '';
                this.descuento = 0;
                this.ivaPorcentaje = parseFloat(proyecto.iva_porcentaje) || 0;
                this.descuentoPorcentaje = parseFloat(proyecto.descuento_porcentaje) || 0;
                this.editarConfiguracion = false;
                this.cotizacionBloqueada = (proyecto.tiene_pagos > 0);
                this.cotizacionAutorizada = false;
                this.cotizacionActualId = null;
                this.tiempo_entrega = ''; // Se mantiene para SH, no afecta a otros.
                this.observaciones = 'Al aceptar esta cotización, está conforme y de acuerdo a los términos y condiciones proporcionados por el asesor de venta.';
                this.mostrarModal = true;
                
                // Cargar artículos
                try {
                    const response = await fetch(`{{ url('/erp/articulos-proyecto') }}/${proyecto.proyecto_id}`);
                    const data = await response.json();
                    this.articulos = data.map(item => ({
                        ...item,
                        precio_unitario: (item.precio !== null && item.precio !== '' && item.precio !== undefined) ? parseFloat(item.precio) : '', // Vacío si no hay precio
                        adicional_unitario: (item.adicional_unitario !== null && item.adicional_unitario !== undefined) ? parseFloat(item.adicional_unitario) : 0 
                    }));

                    // Intentar cargar datos de cotización previa (Envío, Descuento)
                    const resCot = await fetch(`{{ url('/erp/obtener-cotizacion') }}/${proyecto.proyecto_id}`);
                    if (resCot.ok) {
                        const cotizacion = await resCot.json();
                        if (cotizacion) {
                            this.costoEnvio = (cotizacion.envio !== null && cotizacion.envio !== '') ? parseFloat(cotizacion.envio) : '';
                            this.costoInstalacion = (cotizacion.instalacion !== null && cotizacion.instalacion !== '') ? parseFloat(cotizacion.instalacion) : '';
                            // Solo cargar descuento manual si no hay porcentaje fijo de cliente
                            if (this.descuentoPorcentaje == 0) {
                                this.descuento = parseFloat(cotizacion.descuento) || 0;
                            }
                            this.cotizacionActualId = cotizacion.cotizacion_id;
                            this.cotizacionAutorizada = (cotizacion.autorizado == 1);
                        }
                    }
                } catch (error) {
                    console.error('Error cargando artículos:', error);
                    alert('No se pudieron cargar los artículos del proyecto.');
                }
            },

            cerrarModal() {
                this.mostrarModal = false;
                this.proyecto = null;
            },

            get esCotizacionValida() {
                // 1. No debe haber artículos sin precio asignado (diferente de vacío o nulo)
                const preciosInvalidos = this.articulos.some(item => 
                    item.precio_unitario === '' || 
                    item.precio_unitario === null || 
                    isNaN(parseFloat(item.precio_unitario)) || 
                    parseFloat(item.precio_unitario) < 0
                );
                if (preciosInvalidos) {
                    return false;
                }

                // 2. El costo de envío debe ser un número válido (puede ser 0)
                if (this.costoEnvio === '' || this.costoEnvio === null || isNaN(parseFloat(this.costoEnvio)) || parseFloat(this.costoEnvio) < 0) {
                    return false;
                }
                if (this.costoInstalacion === '' || this.costoInstalacion === null || isNaN(parseFloat(this.costoInstalacion)) || parseFloat(this.costoInstalacion) < 0) {
                    return false;
                }

                // 3. Debe haber al menos un artículo en la cotización
                if (this.articulos.length === 0) {
                    return false;
                }

                return true;
            },

            get subtotalArticulos() {
                return this.articulos.reduce((sum, item) => sum + (item.cantidad * ((parseFloat(item.precio_unitario) || 0) + (parseFloat(item.adicional_unitario) || 0))), 0);
            },

            get subtotalGeneral() {
                let descuentoAplicado = parseFloat(this.descuento) || 0;
                if (this.descuentoPorcentaje > 0) {
                    descuentoAplicado = this.subtotalArticulos * (this.descuentoPorcentaje / 100);
                }
                return Math.max(0, this.subtotalArticulos + (parseFloat(this.costoEnvio) || 0) + (parseFloat(this.costoInstalacion) || 0) - descuentoAplicado);
            },

            get iva() {
                return this.subtotalGeneral * (this.ivaPorcentaje / 100);
            },

            get descuentoCalculado() {
                if (this.descuentoPorcentaje > 0) {
                    return this.subtotalArticulos * (this.descuentoPorcentaje / 100);
                }
                return parseFloat(this.descuento) || 0;
            },

            get totalPagar() {
                return this.subtotalGeneral + this.iva;
            },

            get totalCubicaje() {
                return this.articulos.reduce((sum, item) => sum + ((parseFloat(item.cubicaje) || 0) * (parseInt(item.cantidad) || 0)), 0);
            },

            get totalPeso() {
                return this.articulos.reduce((sum, item) => sum + ((parseFloat(item.peso) || 0) * (parseInt(item.cantidad) || 0)), 0);
            },

            get totalArticulos() {
                return this.articulos.reduce((sum, item) => sum + (parseInt(item.cantidad) || 0), 0);
            },

            money(value) {
                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('es-MX');
            },

            async abrirModalPagos(proyecto) {
                this.proyectoPagos = { id: proyecto.proyecto_id, nombre: proyecto.nombre_proyecto };
                this.planBloqueado = (proyecto.tiene_pagos > 0);
                this.rfcRemision = proyecto.rfc || '';
                this.condicionesRemision = proyecto.condiciones_pago || '';
                this.articulosPagos = [];
                this.porcentajeGlobalTapier = 0;
                
                this.instalacionTotal = 0;
                this.instalacionTapierPorcentaje = 0;
                this.instalacionTapierMonto = 0;

                // Cargar los artículos para el desglose de pago Tapier
                try {
                    const resArt = await fetch(`{{ url('/erp/articulos-proyecto') }}/${proyecto.proyecto_id}`);
                    if (resArt.ok) {
                        const arts = await resArt.json();
                        this.articulosPagos = arts.map(a => {
                            const precio = parseFloat(a.precio) || 0;
                            const adicional = parseFloat(a.adicional_unitario) || 0;
                            const cantidad = parseInt(a.cantidad) || 1;
                            const total = (precio + adicional) * cantidad;
                            return {
                                ...a,
                                total_calculado: total,
                                pago_tapier_porcentaje: parseFloat(a.pago_tapier_porcentaje) || 0,
                                pago_tapier_monto: parseFloat(a.pago_tapier_monto) || 0
                            };
                        });
                    }
                } catch(err) { console.error('Error cargando artículos para pagos Tapier:', err); }

                // Obtener el total de la cotización guardada
                try {
                    const response = await fetch(`{{ url('/erp/obtener-cotizacion') }}/${proyecto.proyecto_id}`);
                    if (response.ok) {
                        const cotizacion = await response.json();
                        if (cotizacion && cotizacion.total) {
                            this.totalRemision = parseFloat(cotizacion.total);
                            this.totalCotizacionOriginal = parseFloat(cotizacion.total);
                            
                            this.instalacionTotal = parseFloat(cotizacion.instalacion) || 0;
                            this.instalacionTapierPorcentaje = parseFloat(cotizacion.instalacion_tapier_porcentaje) || 0;
                            this.instalacionTapierMonto = parseFloat(cotizacion.instalacion_tapier_monto) || 0;
                            
                            // Intentar cargar plan de pagos existente
                            let planCargado = false;
                            try {
                                const isSH = proyecto.nombre_proyecto.toUpperCase().startsWith('SH-');
                                const resPlan = await fetch(`{{ url('/erp/plan-pagos') }}/${cotizacion.cotizacion_id}?is_sh=${isSH ? 1 : 0}`);
                                if (resPlan.ok) {
                                    const plan = await resPlan.json();
                                    if (plan && plan.length > 0) {
                                        this.listaPagos = plan.map(p => ({
                                            nombre: p.nombre,
                                            porcentaje: parseFloat(p.porcentaje),
                                            monto: parseFloat(p.monto),
                                            monto_pagado: parseFloat(p.monto_pagado || 0)
                                        }));
                                        this.numeroPagos = plan.length;
                                        if (this.listaPagos.some(p => p.monto_pagado > 0)) this.planBloqueado = true;
                                        planCargado = true;
                                        
                                        this.totalRemision = this.listaPagos.reduce((sum, p) => sum + p.monto, 0);
                                    }
                                }
                            } catch(err) { console.error(err); }

                            if (!planCargado) {
                                const isSH = proyecto.nombre_proyecto.toUpperCase().startsWith('SH-');
                                this.totalRemision = isSH ? null : this.totalCotizacionOriginal;

                                this.numeroPagos = 2; // Resetear a default
                                this.listaPagos = [];
                                if (!isSH) {
                                    this.generarPagos(); // Generar lista inicial
                                }
                            }
                            this.mostrarModalPagos = true;
                        } else {
                            alert('Este proyecto no tiene una cotización guardada o el total es 0. Por favor cotice primero.');
                        }
                    } else {
                        alert('Error al obtener la información de la cotización.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión.');
                }
            },

            generarPagos() {
                if (this.totalRemision === null) return;
                const n = parseInt(this.numeroPagos);
                if (n < 1) return;
                
                // Limitar a máximo 10 pagos
                if (n > 10) {
                    this.numeroPagos = 10;
                    return this.generarPagos();
                }
                
                this.listaPagos = [];
                
                // Definir porcentajes por defecto según la cantidad de pagos solicitada
                let porcentajes = [];
                if (n === 1) {
                    porcentajes = [100];
                } else if (n === 2) {
                    porcentajes = [80, 20];
                } else if (n === 3) {
                    porcentajes = [50, 40, 10];
                } else {
                    const pct = parseFloat((100 / n).toFixed(2));
                    porcentajes = Array(n).fill(pct);
                }
                
                for(let i=0; i<n; i++) {
                    let nombre = '';
                    if (n === 1) nombre = 'Pago Único (Liquidación)';
                    else if (i === 0) nombre = 'Anticipo';
                    else if (i === n - 1) nombre = 'Liquidación';
                    else nombre = `Pago ${i}`; // Consecutivos intermedios
                    
                    this.listaPagos.push({
                        nombre: nombre,
                        porcentaje: porcentajes[i],
                        monto: 0
                    });
                }
                
                // Ajustar el último para que sume 100 exacto si hay decimales periódicos
                const sumaActual = this.listaPagos.reduce((acc, p) => acc + p.porcentaje, 0);
                if (sumaActual !== 100) {
                    const diff = 100 - sumaActual;
                    this.listaPagos[n-1].porcentaje = parseFloat((this.listaPagos[n-1].porcentaje + diff).toFixed(2));
                }
                
                this.calcularMontos();
            },

            calcularMontos() {
                this.listaPagos.forEach(pago => {
                    const pct = parseFloat(pago.porcentaje) || 0;
                    pago.monto = (this.totalRemision * pct) / 100;
                });
            },

            get sumaPorcentajes() {
                return this.listaPagos.reduce((acc, p) => acc + (parseFloat(p.porcentaje) || 0), 0);
            },

            aplicarPorcentajeGlobalTapier() {
                const pct = parseFloat(this.porcentajeGlobalTapier) || 0;
                this.articulosPagos.forEach(art => {
                    art.pago_tapier_porcentaje = pct;
                    art.pago_tapier_monto = (art.total_calculado * pct) / 100;
                });
                
                this.instalacionTapierPorcentaje = pct;
                this.instalacionTapierMonto = (this.instalacionTotal * pct) / 100;
            },

            calcularMontoTapierArticulo(art) {
                const pct = parseFloat(art.pago_tapier_porcentaje) || 0;
                art.pago_tapier_monto = (art.total_calculado * pct) / 100;
            },
            
            calcularMontoTapierInstalacion() {
                const pct = parseFloat(this.instalacionTapierPorcentaje) || 0;
                this.instalacionTapierMonto = (this.instalacionTotal * pct) / 100;
            },

            guardarTotalTapier() {
                const sumaTapierArticulos = this.articulosPagos.reduce((sum, art) => sum + (parseFloat(art.pago_tapier_monto) || 0), 0);
                const sumaTapier = sumaTapierArticulos + (parseFloat(this.instalacionTapierMonto) || 0);
                if (sumaTapier <= 0) {
                    alert('El monto de Pago Tapier debe ser mayor a 0 para generar el plan de pagos.');
                    return;
                }
                this.totalRemision = sumaTapier;
                this.generarPagos();
                alert('Monto actualizado en el Total a Pagar. Puede revisar el Calendario de Pagos.');
            },

            async guardarCotizacion(silent = false) {
                const data = {
                    proyecto: this.proyecto,
                    articulos: this.articulos,
                    totales: {
                        subtotal_articulos: this.subtotalArticulos,
                        envio: this.costoEnvio || 0, // Permite guardar 0 o vacío
                        instalacion: this.costoInstalacion || 0,
                        descuento: this.descuentoCalculado,
                        subtotal: this.subtotalGeneral,
                        iva: this.iva,
                        iva_porcentaje: this.ivaPorcentaje,
                        descuento_porcentaje: this.descuentoPorcentaje,
                        total: this.totalPagar
                    }
                };

                try {
                    const response = await fetch('{{ route("guardarCotizacion") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        this.cotizacionActualId = result.cotizacion_id;
                        if (!silent) {
                            alert('Información guardada correctamente.');
                            this.cotizacionAutorizada = false; // Se reinicia para forzar nueva autorización de los cambios
                        }
                        return result;
                    } else {
                        alert('Error al guardar la información.');
                        return null;
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión');
                    return null;
                }
            },

            async autorizarCotizacion() {
                if (!this.proyecto) return;
                if (!this.esCotizacionValida) {
                    alert('Debe completar todos los precios y el costo de envío antes de autorizar.');
                    return;
                }
                if (!confirm('¿Está seguro de autorizar internamente esta cotización?')) return;
                
                this.autorizando = true;
                try {
                    // Guardar antes de autorizar para asegurar que la base de datos tiene la versión actual de la pantalla
                    const saveResult = await this.guardarCotizacion(true);
                    if (!saveResult || !saveResult.success) {
                        alert('Error al guardar la cotización previo a la autorización.');
                        this.autorizando = false;
                        return;
                    }

                    const response = await fetch('{{ route("autorizarCotizacionInterna") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ proyecto_id: this.proyecto.proyecto_id })
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok && result.success) {
                        alert('Cotización autorizada y registrada en el historial correctamente.');
                        this.cotizacionAutorizada = true;
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo autorizar la cotización.'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión al autorizar la cotización.');
                } finally {
                    this.autorizando = false;
                }
            },

            async ajustarCotizacion() {
                if (!this.proyecto) return;
                if (!confirm('¿Está seguro de querer ajustar la cotización? Esto se registrará en el historial y deberá ser autorizada nuevamente.')) return;
                
                this.ajustando = true;
                try {
                    const response = await fetch('{{ route("ajustarCotizacionInterna") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ proyecto_id: this.proyecto.proyecto_id })
                    });
                    
                    const result = await response.json();
                    if (response.ok && result.success) {
                        this.cotizacionAutorizada = false;
                        alert('Cotización desbloqueada. Puede realizar sus ajustes.');
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo desbloquear la cotización.'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión al intentar ajustar la cotización.');
                } finally {
                    this.ajustando = false;
                }
            },

            async desbloquearProyecto(proyecto) {
                if (!confirm(`¿Está seguro de desbloquear el proyecto "${proyecto.nombre_proyecto}"?\n\nEsto eliminará el plan de pagos actual de forma permanente. Si existen abonos registrados, el monto se transferirá íntegro al "Saldo a Favor" global del cliente para que pueda usarlo de nuevo en este u otro proyecto.`)) {
                    return;
                }

                try {
                    const response = await fetch('{{ url("erp/desbloquear-proyecto") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ proyecto_id: proyecto.proyecto_id })
                    });
                    const result = await response.json();

                    if (response.ok && result.success) {
                        let msg = 'Proyecto desbloqueado correctamente.';
                        if (result.transferido > 0) {
                            msg += `\n\nSe han transferido ${this.money(result.transferido)} al saldo a favor global del cliente.`;
                        }
                        alert(msg);
                        
                        proyecto.tiene_pagos = 0;
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo desbloquear el proyecto.'));
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión al intentar desbloquear el proyecto.');
                }
            },

                    async generarRemisionConPagos(tipo = 'solferino') {                
                    try {
                    // Validar suma 100%
                    if (Math.abs(this.sumaPorcentajes - 100) > 0.1) {
                        alert('La suma de los porcentajes debe ser 100%. Actual: ' + this.sumaPorcentajes.toFixed(2) + '%');
                        return;
                    }
                     if (this.totalCotizacionOriginal === null || this.totalCotizacionOriginal <= 0) {
                        alert('El total original de la cotización no es válido.');
                        return;
                    }

                    let pagosParaEnviar = [];
                    let sumaTapierArticulos = this.articulosPagos.reduce((sum, art) => sum + (parseFloat(art.pago_tapier_monto) || 0), 0);
                    let sumaTapier = sumaTapierArticulos + (parseFloat(this.instalacionTapierMonto) || 0);

                    if (sumaTapier <= 0 && this.isSHProjectPagos) {
                        alert('Debe establecer los porcentajes/montos de Pago Tapier (mayor a 0) antes de generar esta remisión.');
                        return;
                    }

                    let pagosTapier = this.listaPagos.map(p => ({
                        ...p,
                        monto: this.isSHProjectPagos ? ((sumaTapier * parseFloat(p.porcentaje)) / 100) : ((this.totalCotizacionOriginal * parseFloat(p.porcentaje)) / 100)
                    }));

                    let pagosSolferino = this.listaPagos.map(p => ({
                        ...p,
                        monto: (this.totalCotizacionOriginal * parseFloat(p.porcentaje)) / 100
                    }));

                    if (tipo === 'tapier') {
                        pagosParaEnviar = pagosTapier;
                    } else {
                        pagosParaEnviar = pagosSolferino;
                    }

                    // Validar que tengamos datos del proyecto
                    if (!this.proyectoPagos || !this.proyectoPagos.id) {
                        throw new Error("No se ha seleccionado un proyecto válido.");
                    }

                    const data = {
                        proyecto_id: this.proyectoPagos.id,
                        pagos: pagosParaEnviar,
                        pagos_tapier: pagosTapier,
                        rfc: this.rfcRemision,
                        condiciones: this.condicionesRemision,
                        articulos_pagos: this.articulosPagos,
                        instalacion_tapier_porcentaje: this.instalacionTapierPorcentaje,
                        instalacion_tapier_monto: this.instalacionTapierMonto,
                        tipo_remision: tipo
                    };

                    const response = await fetch('{{ route("generarRemisionPdf") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        // Asegurar nombre de archivo
                        const sufijo = tipo === 'tapier' ? '_Tapier' : '_Solferino';
                        const nombreArchivo = this.proyectoPagos.nombre ? `Remision${sufijo}_${this.proyectoPagos.nombre}.pdf` : `Remision${sufijo}.pdf`;
                        a.download = nombreArchivo;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        this.mostrarModalPagos = false;
                    } else {
                        // Intentar leer el mensaje de error del servidor
                        let mensajeError = 'Error al generar la remisión.';
                        try {
                            const res = await response.json();
                            if (res.error) mensajeError += '\nDetalle: ' + res.error;
                        } catch (err) {
                            console.error('No se pudo leer el error JSON', err);
                        }
                        alert(mensajeError);
                    }
                } catch (e) {
                    console.error(e);
                    // Mostrar el mensaje real del error
                    alert('Ocurrió un error: ' + (e.message || 'Error desconocido'));
                }
            },

            async abrirModalProduccion(proyecto) {
                this.proyectoProduccion = proyecto;
                this.articulosProduccion = [];
                this.tiempoEntregaProduccion = '';
                this.mostrarModalProduccion = true;
                
                try {
                    const response = await fetch(`{{ url('/erp/articulos-proyecto') }}/${proyecto.proyecto_id}`);
                    const data = await response.json();
                    this.articulosProduccion = data.map(item => {
                        const impreso = item.impreso_produccion == 1;
                        return {
                            ...item,
                            impreso: impreso,
                            seleccionado: !impreso, // Auto-select si están pendientes
                            desbloqueado: false
                        };
                    });
                } catch (error) {
                    console.error(error);
                    alert('Error cargando artículos para producción.');
                }
            },

            toggleDesbloqueo(item) {
                item.desbloqueado = !item.desbloqueado;
                if (!item.desbloqueado) {
                    item.seleccionado = false;
                }
            },

            async imprimirSeleccionadosPdf() {
                const seleccionados = this.articulosProduccion.filter(a => a.seleccionado).map(a => a.id);
                if (seleccionados.length === 0) {
                    alert('Debe seleccionar al menos un artículo para imprimir.');
                    return;
                }

                this.generandoPdf = true;
                try {
                    const data = {
                        proyecto_id: this.proyectoProduccion.proyecto_id,
                        articulos_ids: seleccionados,
                        tiempo_entrega: this.tiempoEntregaProduccion
                    };

                    const response = await fetch('{{ route("generarProduccionPdf") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const nombreArchivo = this.proyectoProduccion.nombre_proyecto ? `Produccion_${this.proyectoProduccion.nombre_proyecto}.pdf` : 'Produccion.pdf';
                        a.download = nombreArchivo;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        
                        // Actualizar estado localmente para reflejar la modificación de la BD
                        this.articulosProduccion.forEach(a => {
                            if (a.seleccionado) {
                                a.impreso = true;
                                a.desbloqueado = false;
                                a.seleccionado = false;
                            }
                        });
                        
                        this.mostrarModalProduccion = false;
                    } else {
                        let mensajeError = 'Error al generar el PDF de Producción.';
                        try {
                            const res = await response.json();
                            if (res.error) mensajeError += '\nDetalle: ' + res.error;
                        } catch (err) {
                            console.error(err);
                        }
                        alert(mensajeError);
                    }
                } catch (e) {
                    console.error(e);
                    alert('Ocurrió un error: ' + (e.message || 'Error desconocido'));
                } finally {
                    this.generandoPdf = false;
                }
            },

            async abrirModalActa(proyecto) {
                this.proyectoActa = proyecto;
                this.partidaActa = proyecto.partida_acta || '';
                this.articulosActa = [];
                this.mostrarModalActa = true;
                
                try {
                    const response = await fetch(`{{ url('/erp/articulos-proyecto') }}/${proyecto.proyecto_id}`);
                    const data = await response.json();
                    this.articulosActa = data.map(item => {
                        const impreso = item.impreso_acta == 1;
                        return {
                            ...item,
                            impreso: impreso,
                            seleccionado: !impreso,
                            desbloqueado: false,
                            comentarios_acta: item.comentarios_acta || ''
                        };
                    });
                    this.seleccionarTodosActa = this.articulosActa.some(a => a.seleccionado);
                } catch (error) {
                    console.error(error);
                    alert('Error cargando artículos para el acta.');
                }
            },

            toggleSeleccionarTodosActa() {
                this.articulosActa.forEach(a => {
                    if (!(a.impreso && !a.desbloqueado)) {
                        a.seleccionado = this.seleccionarTodosActa;
                    }
                });
            },

            toggleDesbloqueoActa(item) {
                item.desbloqueado = !item.desbloqueado;
                if (!item.desbloqueado) {
                    item.seleccionado = false;
                }
            },

            async imprimirActaSeleccionadosPdf() {
                const seleccionados = this.articulosActa.filter(a => a.seleccionado).map(a => ({
                    id: a.id,
                    comentarios: a.comentarios_acta
                }));
                
                if (seleccionados.length === 0) {
                    alert('Debe seleccionar al menos un artículo para generar el acta.');
                    return;
                }
                this.generandoActaPdf = true;
                try {
                    const data = {
                        proyecto_id: this.proyectoActa.proyecto_id,
                        articulos: seleccionados,
                        partida: this.partidaActa
                    };

                    const response = await fetch('{{ url("erp/generar-acta-entrega") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        const nombreArchivo = this.proyectoActa.nombre_proyecto ? `Acta_Entrega_${this.proyectoActa.nombre_proyecto}.pdf` : 'Acta_Entrega.pdf';
                        a.download = nombreArchivo;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        this.proyectoActa.partida_acta = this.partidaActa;
                        
                        this.articulosActa.forEach(a => {
                            if (a.seleccionado) {
                                a.impreso = true;
                                a.desbloqueado = false;
                                a.seleccionado = false;
                            }
                        });
                        this.mostrarModalActa = false;
                    } else {
                        alert('Error al generar el acta de entrega.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Ocurrió un error: ' + (e.message || 'Error desconocido'));
                } finally {
                    this.generandoActaPdf = false;
                }
            },

            async imprimirPdf() {
                if (!this.esCotizacionValida) {
                    alert('Por favor, complete todos los precios y el costo de envío para generar el PDF.');
                    return;
                }
                if (!this.cotizacionAutorizada) {
                    alert('Debe autorizar la cotización internamente antes de generar el PDF.');
                    return;
                }

                const data = {
                    proyecto: this.proyecto,
                    articulos: this.articulos,
                    totales: {
                        subtotal_articulos: this.subtotalArticulos,
                        envio: this.costoEnvio,
                        instalacion: this.costoInstalacion,
                        descuento: this.descuentoCalculado, // Enviamos el calculado para que sea exacto
                        subtotal: this.subtotalGeneral,
                        iva: this.iva,
                        iva_porcentaje: this.ivaPorcentaje, // Enviamos el % por si se necesita en PDF
                        descuento_porcentaje: this.descuentoPorcentaje,
                        total: this.totalPagar,
                        cubicaje: this.totalCubicaje,
                        peso: this.totalPeso,
                        articulos: this.totalArticulos
                    },
                    cotizacionId: this.cotizacionActualId,
                    tiempo_entrega: this.tiempo_entrega,
                    observaciones: this.observaciones
                };

                // Enviar a backend para generar PDF
                try {
                    const response = await fetch('{{ route("generarCotizacionPdf") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        // Convertir la respuesta a Blob (archivo)
                        const blob = await response.blob();
                        // Crear una URL temporal para el archivo
                        const url = window.URL.createObjectURL(blob);
                        // Crear un enlace invisible y hacer clic en él para descargar
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Cotizacion_${this.proyecto.nombre_proyecto}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        alert('PDF descargado correctamente.');
                    } else {
                        // Intentar obtener el mensaje de error del servidor
                        try {
                            const errorJson = await response.json();
                            console.error('Error detallado:', errorJson);
                            alert('Error al generar PDF: ' + (errorJson.error || 'Error desconocido'));
                        } catch (e) {
                            const errorText = await response.text();
                            console.error('Error del servidor (HTML):', errorText);
                            alert('Error crítico del servidor. Revisa la consola.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión');
                }
            }
        }
    }
</script>

@endsection
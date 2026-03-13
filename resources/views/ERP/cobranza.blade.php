@extends('principal')

@section('contenido')

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

<main class="flex-1 flex flex-col h-screen bg-gray-100" x-data="cobranzaApp()">
    <!-- Header -->
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-bank text-purple-600 mr-2"></i> Módulo de Cobranza
        </h2>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Lista de Proyectos (Columna Izquierda) -->
        <div class="w-1/3 bg-white border-r overflow-y-auto">
            <div class="p-4 border-b sticky top-0 bg-white z-10">
                <input type="text" x-model="filtro" placeholder="Buscar por proyecto o cliente..." class="w-full text-sm border-gray-200 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div x-show="cargando" class="p-4 text-center text-gray-500">Cargando...</div>
            <div x-show="!cargando">
                <template x-for="proyecto in proyectosFiltrados" :key="proyecto.proyecto_id">
                    <button @click="seleccionarProyecto(proyecto)" 
                            class="w-full text-left p-4 border-b hover:bg-purple-50 transition focus:outline-none"
                            :class="{ 'bg-purple-100 border-l-4 border-purple-500': proyectoSeleccionado && proyecto.proyecto_id === proyectoSeleccionado.proyecto_id }">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-sm text-gray-800" x-text="proyecto.nombre_proyecto"></p>
                                <p class="text-xs text-gray-500" x-text="proyecto.cliente_nombre"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-sm text-purple-700" x-text="money(proyecto.total_cotizacion)"></p>
                                <p class="text-xs font-bold" :class="proyecto.saldo_pendiente > 0.01 ? 'text-red-600' : 'text-green-600'" x-text="proyecto.saldo_pendiente > 0.01 ? 'Pendiente' : 'Pagado'"></p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-green-500 h-1.5 rounded-full" :style="`width: ${(proyecto.total_pagado / proyecto.total_plan * 100) || 0}%`"></div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Detalles del Proyecto (Columna Derecha) -->
        <div class="w-2/3 p-8 overflow-y-auto bg-gray-50">
            <template x-if="!proyectoSeleccionado">
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <i class="ph ph-arrow-left text-6xl mb-4"></i>
                    <p class="text-lg font-medium">Seleccione un proyecto para ver su plan de pagos.</p>
                </div>
            </template>

            <template x-if="proyectoSeleccionado">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800" x-text="proyectoSeleccionado.nombre_proyecto"></h3>
                    <p class="text-sm text-gray-500 mb-6" x-text="`Cliente: ${proyectoSeleccionado.cliente_nombre}`"></p>

                    <!-- Resumen de Pagos -->
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-bold">Total del Proyecto</p>
                            <p class="text-2xl font-bold text-gray-800" x-text="money(proyectoSeleccionado.total_plan)"></p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-bold">Total Pagado</p>
                            <p class="text-2xl font-bold text-green-600" x-text="money(proyectoSeleccionado.total_pagado || 0)"></p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-bold">Saldo Pendiente</p>
                            <p class="text-2xl font-bold text-red-600" x-text="money(proyectoSeleccionado.saldo_pendiente || 0)"></p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border shadow-sm">
                            <p class="text-xs text-gray-500 uppercase font-bold">Saldo a Favor</p>
                            <p class="text-2xl font-bold text-blue-600" x-text="money(proyectoSeleccionado.saldo_afavor || 0)"></p>
                        </div>
                    </div>

                    <!-- Tabla de Plan de Pagos -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Concepto</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Monto Acordado</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Monto Pagado</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Pendiente</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estatus</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Registrar Abono</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="pago in planPagos" :key="pago.id">
                                    <tr :class="{ 'bg-green-50': pago.estatus === 'pagado' }">
                                        <td class="px-4 py-3 text-sm font-bold text-gray-800" x-text="pago.nombre"></td>
                                        <td class="px-4 py-3 text-sm text-right" x-text="money(pago.monto)"></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-green-700" x-text="money(pago.monto_pagado)"></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-red-700" x-text="money(pago.monto - pago.monto_pagado)"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full"
                                                  :class="{
                                                      'bg-green-100 text-green-800': pago.estatus === 'pagado',
                                                      'bg-yellow-100 text-yellow-800': pago.estatus === 'parcial',
                                                      'bg-red-100 text-red-800': pago.estatus === 'pendiente'
                                                  }"
                                                  x-text="pago.estatus">
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <form @submit.prevent="registrarAbono(pago)" x-show="pago.estatus !== 'pagado'">
                                                <div class="flex items-center gap-2">
                                                    <div class="relative">
                                                        <span class="absolute left-2 top-1.5 text-gray-500 text-xs">$</span>
                                                        <input type="number" step="0.01" x-model="pago.abono" placeholder="0.00" class="w-28 pl-5 text-sm border-gray-300 rounded focus:ring-purple-500 focus:border-purple-500" title="Ingrese el monto recibido (el excedente pasará al siguiente pago)">
                                                    </div>
                                                    <button type="submit" class="p-2 bg-purple-600 text-white rounded hover:bg-purple-700" title="Guardar Abono">
                                                        <i class="ph ph-floppy-disk"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>

<script>
function cobranzaApp() {
    return {
        proyectos: @json($proyectos),
        filtro: '',
        proyectoSeleccionado: null,
        planPagos: [],
        cargando: false,

        get proyectosFiltrados() {
            if (this.filtro === '') return this.proyectos;
            const busqueda = this.filtro.toLowerCase();
            return this.proyectos.filter(p => 
                p.nombre_proyecto.toLowerCase().includes(busqueda) ||
                p.cliente_nombre.toLowerCase().includes(busqueda)
            );
        },

        async seleccionarProyecto(proyecto) {
            this.proyectoSeleccionado = proyecto;
            this.cargando = true;
            this.planPagos = [];

            if (!proyecto.cotizacion_id) {
                this.cargando = false;
                alert('Este proyecto no tiene una cotización válida asociada.');
                return;
            }

            try {
                const response = await fetch(`{{ url('/erp/plan-pagos') }}/${proyecto.cotizacion_id}`);
                if (!response.ok) throw new Error('Error de red');
                let plan = await response.json();
                this.planPagos = plan.map(p => ({...p, abono: ''}));
            } catch (error) {
                console.error('Error al cargar plan de pagos:', error);
                alert('No se pudo cargar el plan de pagos.');
            } finally {
                this.cargando = false;
            }
        },

        async registrarAbono(pago) {
            if (!pago.abono || parseFloat(pago.abono) <= 0) {
                alert('Por favor, ingrese un monto de abono válido.'); return;
            }

            try {
                const response = await fetch('{{ route("registrarPago") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ pago_id: pago.id, monto_abono: pago.abono })
                });
                const result = await response.json();
                if (result.success) {
                    // alert(result.message); // Opcional: quitar alerta intrusiva si se ve en la UI
                    this.planPagos = result.plan.map(p => ({...p, abono: ''}));
                    if (typeof result.saldo_afavor !== 'undefined') {
                        this.proyectoSeleccionado.saldo_afavor = result.saldo_afavor;
                    }
                    this.actualizarResumenProyecto();
                } else {
                    alert('Error: ' + (result.message || 'Ocurrió un error.'));
                }
            } catch (error) {
                console.error('Error al registrar pago:', error);
                alert('Error de conexión al registrar el pago.');
            }
        },

        actualizarResumenProyecto() {
            const totalPagado = this.planPagos.reduce((sum, p) => sum + parseFloat(p.monto_pagado), 0);
            const totalPlan = this.planPagos.reduce((sum, p) => sum + parseFloat(p.monto), 0);
            
            this.proyectoSeleccionado.total_pagado = totalPagado;
            this.proyectoSeleccionado.saldo_pendiente = totalPlan - totalPagado;

            const index = this.proyectos.findIndex(p => p.proyecto_id === this.proyectoSeleccionado.proyecto_id);
            if (index !== -1) {
                this.proyectos[index].total_pagado = totalPagado;
                this.proyectos[index].saldo_pendiente = totalPlan - totalPagado;
                this.proyectos[index].saldo_afavor = this.proyectoSeleccionado.saldo_afavor;
            }
        },

        money(value) {
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
        }
    }
}
</script>

@endsection
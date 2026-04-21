@extends('principal')

@section('contenido')

@php
    $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
    $role = strtoupper($userRoleName);
    $isAdmin = in_array($role, ['ADMIN', 'DIRECCIÓN', 'DIRECCION']);
    $isVendedor = $role === 'VENDEDOR/DISEÑADOR';
    $isAdminCobranza = in_array($role, ['ADMINISTRACIÓN', 'ADMINISTRACION']);
    $isDvMkt = in_array($role, ['COORD. DV&MKT']);
    $isDvSolferino = in_array($role, ['COORD. DV SOLFERINO']);
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
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800" x-text="proyectoSeleccionado.nombre_proyecto"></h3>
                            <p class="text-sm text-gray-500" x-text="`Cliente: ${proyectoSeleccionado.cliente_nombre}`"></p>
                        </div>
                        
                        <!-- Medidor de Porcentaje Pagado -->
                        <div class="flex flex-col items-center justify-center w-40">
                            <div class="relative w-32 h-16">
                                <svg viewBox="0 0 100 55" class="w-full h-full overflow-visible">
                                    <defs>
                                        <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#ef4444" />
                                            <stop offset="50%" stop-color="#eab308" />
                                            <stop offset="100%" stop-color="#22c55e" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="url(#gaugeGradient)" stroke-width="12" stroke-linecap="round" />
                                    
                                    <g :style="`transform: rotate(${ (Math.min(1, Math.max(0, proyectoSeleccionado.total_plan > 0 ? (proyectoSeleccionado.total_pagado / proyectoSeleccionado.total_plan) : 0)) * 180) - 90 }deg); transform-origin: 50px 50px; transition: transform 1s ease-out;`">
                                        <circle cx="50" cy="50" r="6" fill="#374151" />
                                        <polygon points="46,50 54,50 50,15" fill="#374151" stroke="#374151" stroke-width="1" stroke-linejoin="round" />
                                        <circle cx="50" cy="50" r="2" fill="#ffffff" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-xl font-black text-gray-800 leading-none" x-text="`${(Math.min(100, Math.max(0, proyectoSeleccionado.total_plan > 0 ? (proyectoSeleccionado.total_pagado / proyectoSeleccionado.total_plan * 100) : 0))).toFixed(1)}%`"></span>
                                <span class="text-[10px] uppercase font-bold text-gray-500 block leading-none mt-1">Pagado</span>
                            </div>
                        </div>
                    </div>

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
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase" x-show="canCobrar(proyectoSeleccionado)">Registrar Abono</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase" x-show="canValidar(proyectoSeleccionado)">Validación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="pago in planPagos" :key="pago.id">
                                    <tr :class="{ 'bg-green-50': pago.estatus === 'pagado' && pago.validado, 'bg-orange-50': !pago.validado && pago.monto_pagado > 0 }">
                                        <td class="px-4 py-3 text-sm font-bold text-gray-800" x-text="pago.nombre"></td>
                                        <td class="px-4 py-3 text-sm text-right" x-text="money(pago.monto)"></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold" 
                                            :class="pago.validado ? 'text-green-700' : (pago.monto_pagado > 0 ? 'text-yellow-600' : 'text-gray-500')" 
                                            :title="!pago.validado && pago.monto_pagado > 0 ? 'Pago recibido, pendiente de validación' : ''" x-text="money(pago.monto_pagado)"></td>                                        <td class="px-4 py-3 text-sm text-right font-bold text-red-700" x-text="money(pago.monto - pago.monto_pagado)"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full inline-block text-center"
                                                  :class="{
                                                      'bg-green-100 text-green-800': pago.estatus === 'pagado' && pago.validado,
                                                      'bg-orange-100 text-orange-800': !pago.validado && pago.monto_pagado > 0,
                                                      'bg-yellow-100 text-yellow-800': pago.estatus === 'parcial' && pago.validado,
                                                      'bg-red-100 text-red-800': pago.estatus === 'pendiente' && pago.monto_pagado == 0
                                                  }"
                                                  x-text="(!pago.validado && pago.monto_pagado > 0) ? 'Validación pendiente' : pago.estatus.charAt(0).toUpperCase() + pago.estatus.slice(1)">
                                            </span>
                                        </td>
                                        <td class="px-4 py-3" x-show="canCobrar(proyectoSeleccionado)">
                                            <form @submit.prevent="registrarAbono(pago)" x-show="pago.estatus !== 'pagado' && !pago.validado">
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
                                        <td class="px-4 py-3 text-center" x-show="canValidar(proyectoSeleccionado)">
                                            <button type="button" @click="validarPago(pago)" x-show="pago.monto_pagado > 0 && !pago.validado" class="px-3 py-1 bg-green-600 text-white rounded text-xs font-bold hover:bg-green-700 shadow-sm inline-flex items-center" title="Validar Pago">
                                                <i class="ph ph-check-circle mr-1"></i> Validar
                                            </button>
                                            <span x-show="pago.validado" class="text-green-600 font-bold text-xs inline-flex items-center">
                                                <i class="ph ph-shield-check mr-1 text-lg"></i> Validado
                                            </span>
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

    <!-- Modal Confirmación Abono -->
    <div x-show="modalConfirmacion.open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50" style="display: none;" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden" @click.stop>
            <div class="p-4 border-b flex justify-between items-center shrink-0"
                 :class="modalConfirmacion.tipo === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-purple-50 border-purple-200'">
                <h3 class="text-lg font-bold"
                    :class="modalConfirmacion.tipo === 'warning' ? 'text-yellow-800' : 'text-purple-800'">
                    <i class="ph mr-2" :class="modalConfirmacion.tipo === 'warning' ? 'ph-warning text-yellow-600' : 'ph-question text-purple-600'"></i>
                    <span x-text="modalConfirmacion.tipo === 'warning' ? 'Advertencia de Saldo a Favor' : 'Confirmar Abono'"></span>
                </h3>
                <button @click="responderConfirmacion(false)" class="text-gray-500 hover:text-gray-800"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6 bg-white">
                <p class="text-sm text-gray-700 mb-4" x-text="modalConfirmacion.mensajePrincipal"></p>
                <div x-show="modalConfirmacion.detalles.length > 0" class="bg-yellow-50 p-3 rounded border border-yellow-200 mb-4">
                    <template x-for="detalle in modalConfirmacion.detalles">
                        <p class="text-sm font-bold text-yellow-800" x-text="detalle"></p>
                    </template>
                </div>
                <p x-show="modalConfirmacion.tipo === 'warning'" class="text-sm font-bold text-gray-800 text-center">¿Desea continuar de todos modos?</p>
            </div>
            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 shrink-0">
                <button @click="responderConfirmacion(false)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-bold transition">Cancelar</button>
                <button @click="responderConfirmacion(true)"
                        class="px-6 py-2 text-white rounded-lg font-bold transition shadow-sm flex items-center"
                        :class="modalConfirmacion.tipo === 'warning' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-purple-600 hover:bg-purple-700'">
                    <i class="ph ph-check mr-2"></i> Continuar
                </button>
            </div>
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
        isAdmin: @json($isAdmin),
        isVendedor: @json($isVendedor),
        isAdminCobranza: @json($isAdminCobranza),
        isDvMkt: @json($isDvMkt),
        isDvSolferino: @json($isDvSolferino),

        modalConfirmacion: {
            open: false,
            tipo: 'info',
            mensajePrincipal: '',
            detalles: [],
            resolve: null
        },

        async solicitarConfirmacion(tipo, mensajePrincipal, detalles = []) {
            this.modalConfirmacion.tipo = tipo;
            this.modalConfirmacion.mensajePrincipal = mensajePrincipal;
            this.modalConfirmacion.detalles = detalles;
            this.modalConfirmacion.open = true;

            return new Promise((resolve) => {
                this.modalConfirmacion.resolve = resolve;
            });
        },

        responderConfirmacion(respuesta) {
            this.modalConfirmacion.open = false;
            if (this.modalConfirmacion.resolve) {
                this.modalConfirmacion.resolve(respuesta);
                this.modalConfirmacion.resolve = null;
            }
        },

        canCobrar(proyecto) {
            if (!proyecto) return false;
            let emp = (proyecto.empresa_nombre || proyecto.nombre_proyecto || '').toLowerCase();
            let isCT = emp.includes('casa tapier') || emp.startsWith('ct-');
            let isSH = emp.includes('solferino') || emp.startsWith('sh-');
            
            if (this.isAdmin) return true;
            
            if (isCT) {
                return this.isVendedor || this.isAdminCobranza || this.isDvMkt;
            } else if (isSH) {
                return this.isDvMkt;
            }
            return false;
        },
        
        canValidar(proyecto) {
            if (!proyecto) return false;
            let emp = (proyecto.empresa_nombre || proyecto.nombre_proyecto || '').toLowerCase();
            let isCT = emp.includes('casa tapier') || emp.startsWith('ct-');
            let isSH = emp.includes('solferino') || emp.startsWith('sh-');
            
            if (this.isAdmin) return true;
            
            if (isCT) {
                return this.isAdminCobranza || this.isDvMkt;
            } else if (isSH) {
                return this.isDvSolferino;
            }
            return false;
        },

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
                this.actualizarResumenProyecto();
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

            const abono = parseFloat(pago.abono);
            const saldoPendiente = parseFloat(this.proyectoSeleccionado.saldo_pendiente);

            if (abono > saldoPendiente) {
                const saldoFavor = abono - saldoPendiente;
                const confirmado = await this.solicitarConfirmacion(
                    'warning',
                    'Advertencia: Revisa los campos de Cobranza para asegurarte que tu cliente tiene saldo a favor o si la cantidad registrada generará un saldo a favor por algún error.',
                    [
                        `Monto ingresado: ${this.money(abono)}`,
                        `Saldo a favor resultante: ${this.money(saldoFavor)}`
                    ]
                );
                if (!confirmado) return;
            } else {
                const confirmado = await this.solicitarConfirmacion(
                    'info',
                    `¿Está seguro de que desea registrar un abono por ${this.money(abono)}?`
                );
                if (!confirmado) return;
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

        async validarPago(pago) {
            if (!confirm('¿Seguro que desea validar este pago? Esta acción no se puede deshacer.')) return;

            try {
                const response = await fetch('{{ route("registrarPago") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ validar_pago: true, pago_id: pago.id })
                });
                const result = await response.json();
                if (result.success) {
                    this.planPagos = result.plan.map(p => ({...p, abono: ''}));
                    this.actualizarResumenProyecto();
                } else {
                    alert('Error: ' + (result.message || 'Ocurrió un error.'));
                }
            } catch (error) {
                console.error('Error al validar pago:', error);
                alert('Error de conexión al validar el pago.');
            }
        },

        actualizarResumenProyecto() {
            const totalPagado = this.planPagos.reduce((sum, p) => sum + (p.validado ? parseFloat(p.monto_pagado) : 0), 0);         
            const totalPlan = this.planPagos.reduce((sum, p) => sum + parseFloat(p.monto), 0);
            
            this.proyectoSeleccionado.total_pagado = totalPagado;
            this.proyectoSeleccionado.total_plan = totalPlan;
            this.proyectoSeleccionado.saldo_pendiente = totalPlan - totalPagado;

            const index = this.proyectos.findIndex(p => p.proyecto_id === this.proyectoSeleccionado.proyecto_id);
            if (index !== -1) {
                this.proyectos[index].total_pagado = totalPagado;
                this.proyectos[index].total_plan = totalPlan;
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
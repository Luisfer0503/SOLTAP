@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="logisticaApp()">

    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="ph ph-truck text-blue-600 mr-2"></i> Logística y Entregas
                </h2>
            </div>
            
            <div class="bg-gray-100 p-1 rounded-lg flex space-x-1">
                <button @click="tab = 'entregas'" 
                        :class="tab === 'entregas' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-bold transition flex items-center">
                    <i class="ph ph-package mr-2"></i> Planificación de Entrega
                </button>
                <button @click="tab = 'retornos'" 
                        :class="tab === 'retornos' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-bold transition flex items-center">
                    <i class="ph ph-arrow-u-up-left mr-2"></i> Gestión de Retornos
                </button>
            </div>
        </div>

        <div class="relative w-full md:w-96" @click.away="showProyectos = false">
            <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input type="text" 
                   x-model="searchProyecto" 
                   @focus="showProyectos = true"
                   @click="showProyectos = true"
                   placeholder="Buscar proyecto por nombre o cliente..." 
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            
            <div x-show="showProyectos" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                <template x-for="proyecto in filteredProyectos" :key="proyecto.id">
                    <button @click="selectProyecto(proyecto); showProyectos = false; searchProyecto = proyecto.nombre" 
                            class="w-full px-4 py-3 text-left hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="font-bold text-sm text-gray-800" x-text="proyecto.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="proyecto.cliente"></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                  :class="proyecto.estatus === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                  x-text="proyecto.estatus"></span>
                        </div>
                    </button>
                </template>
                <div x-show="filteredProyectos.length === 0" class="px-4 py-3 text-center text-sm text-gray-500">
                    No se encontraron proyectos
                </div>
            </div>
        </div>

        <p x-show="proyectoActual" class="text-sm text-gray-500 mt-3">
            Proyecto: <span class="font-bold text-gray-700" x-text="proyectoActual?.nombre"></span> • 
            <span class="text-gray-600" x-text="'Cliente: ' + proyectoActual?.cliente"></span>
        </p>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        
        <div x-show="tab === 'entregas' && !proyectoActual" class="h-full flex flex-col items-center justify-center text-gray-400 py-12">
            <i class="ph ph-truck text-6xl mb-4"></i>
            <p class="text-lg font-medium">Seleccione un proyecto para gestionar su logística.</p>
        </div>

        <!-- Módulo de Entregas -->
        <div x-show="tab === 'entregas' && proyectoActual" x-cloak x-transition:enter="transition ease-out duration-300">
            
            <!-- Panel Logístico Global del Proyecto -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">
                    Logística Global del Proyecto
                </h3>

                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1">Dirección de Entrega</label>
                        <p class="text-sm text-gray-800 font-medium flex items-start">
                            <i class="ph ph-map-pin text-gray-400 mr-2 mt-0.5"></i>
                            <span x-text="proyectoActual.direccion || 'No especificada'"></span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1">Vendedor Asignado</label>
                        <p class="text-sm text-gray-800 font-medium flex items-start">
                            <i class="ph ph-user-circle text-gray-400 mr-2 mt-0.5"></i>
                            <span x-text="proyectoActual.vendedor_nombre || 'No asignado'"></span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Validación de Acceso</label>
                        <div class="flex gap-4 mb-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" x-model="logisticaForm.es_planta_baja" value="si" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Sí, es Planta Baja</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" x-model="logisticaForm.es_planta_baja" value="no" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">No (Requiere Escaleras...)</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Condiciones de Acceso / Excepciones</label>
                            <textarea x-model="logisticaForm.condiciones_acceso" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="Ej. Escaleras estrechas, horario restringido..."></textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Requerimientos de Entrega</label>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="logisticaForm.requiere_emplaye" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requiere Emplaye</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="logisticaForm.requiere_desemplaye" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requiere Desemplaye</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="logisticaForm.requiere_instalacion" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requiere Instalación</span>
                            </label>
                               <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="logisticaForm.requiere_maniobraje" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requiere Maniobraje</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button @click="guardarLogistica()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow flex items-center transition">
                        <i class="ph ph-floppy-disk mr-2"></i> Guardar Logística del Proyecto
                    </button>
                </div>
            </div>

            <!-- Lista de Artículos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-700">Artículos del Proyecto</h3>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold" x-text="articulosProyecto.length + ' Ítems'"></span>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-for="item in articulosProyecto" :key="item.id">
                        <div class="p-6 hover:bg-gray-50 transition flex items-start">
                            <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mr-4 shrink-0 overflow-hidden border border-gray-200">
                                <template x-if="item.imagen">
                                    <img :src="item.imagen" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.imagen">
                                    <i class="ph ph-package text-2xl"></i>
                                </template>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm" x-text="item.nombre"></h4>
                                <p class="text-xs text-gray-500" x-text="`${item.alto}x${item.ancho}x${item.profundo}cm | ${item.peso}kg | Cantidad: ${item.cantidad}`"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="item.descripcion"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="tab === 'retornos'" x-cloak>
            <div class="max-w-4xl mx-auto">
                
                <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-8 overflow-hidden">
                    <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex items-center">
                        <i class="ph ph-arrow-u-up-left text-orange-600 text-xl mr-2"></i>
                        <h3 class="text-lg font-bold text-orange-800">Registrar Nuevo Retorno</h3>
                    </div>
                    
                    <form class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar Artículo a Retornar</label>
                                <select class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-orange-500">
                                    <option value="">Seleccione...</option>
                                    <template x-for="item in todosArticulos" :key="item.id">
                                        <option :value="item.id" x-text="`${item.nombre} (Proyecto ID: ${item.proyecto_id})`"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Destinatario (Cliente/Origen)</label>
                                <div class="relative">
                                    <i class="ph ph-user absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" placeholder="Persona que devuelve" class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Persona que Recibe (Interno)</label>
                                <div class="relative">
                                    <i class="ph ph-identification-badge absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" placeholder="Staff a cargo" class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ubicación Interna (Destino)</label>
                                <select class="w-full rounded-lg border-gray-300 focus:ring-orange-500">
                                    <option>Almacén General - Nave A</option>
                                    <option>Taller de Reparaciones</option>
                                    <option>Showroom</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Motivo</label>
                                <select class="w-full rounded-lg border-gray-300 focus:ring-orange-500">
                                    <option>Defecto de Fábrica</option>
                                    <option>Daño en Traslado</option>
                                    <option>Cambio de Modelo</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" class="px-6 py-2 bg-orange-600 text-white font-bold rounded-lg shadow hover:bg-orange-700 transition flex items-center">
                                <i class="ph ph-floppy-disk mr-2"></i> Guardar Retorno
                            </button>
                        </div>
                    </form>
                </div>

                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Retornos Activos</h4>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Recibió</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">Silla Eames (Dañada)</td>
                                <td class="px-6 py-4 text-sm text-gray-500">Juan Almacén</td>
                                <td class="px-6 py-4 text-sm text-gray-500">10 Feb 2026</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">En Revisión</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
    function logisticaApp() {
        return {
            tab: 'entregas',
            searchProyecto: '',
            showProyectos: false,
            proyectoActual: null,
            proyectos: @json($proyectos),
            todosArticulos: @json($articulos),
            articulosProyecto: [],
            logisticaForm: {
                proyecto_id: null,
                es_planta_baja: 'si',
                condiciones_acceso: '', // Initialize with default values
                requiere_instalacion: false,
                requiere_desemplaye: false,
                requiere_emplaye: false
            },
            
            get filteredProyectos() {
                if (!this.searchProyecto) return this.proyectos;
                const search = this.searchProyecto.toLowerCase();
                return this.proyectos.filter(p => 
                    (p.nombre && p.nombre.toLowerCase().includes(search)) || 
                    (p.cliente && p.cliente.toLowerCase().includes(search))
                );
            },
            
            selectProyecto(proyecto) {
                this.proyectoActual = proyecto;
                this.articulosProyecto = this.todosArticulos.filter(a => a.proyecto_id == proyecto.id);
                this.logisticaForm = {
                    proyecto_id: proyecto.id,
                    es_planta_baja: proyecto.es_planta_baja == 1 ? 'si' : 'no', // Convert 1/0 from DB to 'si'/'no' for radio buttons
                    condiciones_acceso: proyecto.condiciones_acceso || '',
                    requiere_instalacion: proyecto.requiere_instalacion == 1,
                    requiere_desemplaye: proyecto.requiere_desemplaye == 1,
                    requiere_emplaye: proyecto.requiere_emplaye == 1,
                    requiere_maniobraje: proyecto.requiere_maniobraje == 1,
                };
            },

            async guardarLogistica() {
                try {
                    const response = await fetch('{{ route("guardarLogisticaProyecto") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.logisticaForm)
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Información de logística guardada correctamente.');
                        // Reflejar localmente en la lista
                        const idx = this.proyectos.findIndex(p => p.id === this.proyectoActual.id);
                        if(idx !== -1) {
                            this.proyectos[idx].es_planta_baja = this.logisticaForm.es_planta_baja;
                            this.proyectos[idx].condiciones_acceso = this.logisticaForm.condiciones_acceso;
                            this.proyectos[idx].requiere_instalacion = this.logisticaForm.requiere_instalacion ? 1 : 0;
                            this.proyectos[idx].requiere_desemplaye = this.logisticaForm.requiere_desemplaye ? 1 : 0;
                            this.proyectos[idx].requiere_emplaye = this.logisticaForm.requiere_emplaye ? 1 : 0;
                            this.proyectos[idx].requiere_maniobraje = this.logisticaForm.requiere_maniobraje ? 1 : 0;
                        }
                    } else {
                        alert('Error al guardar: ' + result.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión.');
                }
            }
        }
    }
</script>

@endsection
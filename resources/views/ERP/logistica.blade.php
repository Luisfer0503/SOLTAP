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
                        <div class="mb-3">
                            <span class="block text-xs font-bold text-gray-400 mb-1">¿Es Planta Baja?</span>
                            <p class="text-sm text-gray-800 font-medium" x-text="proyectoActual.es_planta_baja == 1 ? 'Sí, es Planta Baja' : 'No (Requiere Escaleras...)'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Condiciones de Acceso / Excepciones</label>
                            <div class="w-full text-sm border border-gray-200 bg-white rounded p-2 text-gray-700 min-h-[3rem]" x-text="proyectoActual.condiciones_acceso || 'Ninguna especificada.'"></div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Requerimientos de Entrega</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" disabled :checked="proyectoActual.requiere_emplaye == 1" class="w-4 h-4 rounded text-blue-600 border-gray-300 bg-gray-200 cursor-not-allowed">
                                <span class="ml-2 text-sm text-gray-600">Requiere Emplaye</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" disabled :checked="proyectoActual.requiere_desemplaye == 1" class="w-4 h-4 rounded text-blue-600 border-gray-300 bg-gray-200 cursor-not-allowed">
                                <span class="ml-2 text-sm text-gray-600">Requiere Desemplaye</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" disabled :checked="proyectoActual.requiere_instalacion == 1" class="w-4 h-4 rounded text-blue-600 border-gray-300 bg-gray-200 cursor-not-allowed">
                                <span class="ml-2 text-sm text-gray-600">Requiere Instalación</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" disabled :checked="proyectoActual.requiere_maniobraje == 1" class="w-4 h-4 rounded text-blue-600 border-gray-300 bg-gray-200 cursor-not-allowed">
                                <span class="ml-2 text-sm text-gray-600">Requiere Maniobraje</span>
                            </label>
                        </div>
                    </div>
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
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-sm" x-text="item.nombre"></h4>
                                <p class="text-xs text-gray-500" x-text="`${item.alto}x${item.ancho}x${item.profundo}cm | ${item.peso}kg | Cantidad: ${item.cantidad}`"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="item.descripcion"></p>
                            </div>
                            <div class="ml-auto pl-4 flex-shrink-0">
                                <button @click="abrirModalRetorno(item)" class="px-3 py-1.5 bg-orange-100 text-orange-700 hover:bg-orange-200 rounded text-xs font-bold transition flex items-center shadow-sm">
                                    <i class="ph ph-arrow-u-up-left mr-1 text-lg"></i> Retornar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div x-show="tab === 'retornos'" x-cloak>
            <div class="max-w-4xl mx-auto">
                
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
                            <template x-if="retornos.length === 0">
                                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay retornos registrados.</td></tr>
                            </template>
                            <template x-for="retorno in retornos" :key="retorno.id">
                                <tr>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900" x-text="retorno.articulo_nombre || 'Artículo N/A'"></td>
                                    <td class="px-6 py-4 text-sm text-gray-600" x-text="retorno.destinatario"></td>
                                    <td class="px-6 py-4 text-sm text-gray-500" x-text="retorno.fecha_formateada"></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-800" x-text="retorno.estatus"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Retorno -->
    <div x-show="mostrarModalRetorno" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;" x-cloak>
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl flex flex-col overflow-hidden" @click.away="mostrarModalRetorno = false">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center"><i class="ph ph-arrow-u-up-left mr-2"></i> Reportar Retorno de Artículo</h3>
                <button @click="mostrarModalRetorno = false" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            
            <div class="p-6 bg-gray-50 overflow-y-auto max-h-[80vh]">
                <form @submit.prevent="guardarRetorno">
                    <div class="mb-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 border-b pb-2 text-orange-700">Información Automática (Identificación para Logística)</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase">Proyecto</span>
                                <span class="font-semibold text-gray-800" x-text="formRetorno.proyecto_nombre"></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase">Diseñador Responsable</span>
                                <span class="font-semibold text-gray-800" x-text="formRetorno.disenador_nombre"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-xs font-bold text-gray-400 uppercase">Artículo a Retornar</span>
                                <span class="font-semibold text-gray-800 text-base" x-text="formRetorno.articulo_nombre"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Especificación de Destinatario y Ubicación Interna</h4>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Entregar a (Destinatario) <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formRetorno.destinatario" required class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm" placeholder="Nombre de quien recibe el retorno...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nave / Área / Ubicación Interna <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formRetorno.ubicacion_interna" required class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm" placeholder="Ej. Nave 2, Almacén General, Área de Reparación...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Personal a cargo (Recepción/Envío logístico) <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formRetorno.persona_logistica" required class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm" placeholder="Nombre del responsable de logística...">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" @click="mostrarModalRetorno = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                        <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition shadow-md flex items-center" :disabled="guardandoRetorno">
                            <i class="ph ph-floppy-disk mr-2" x-show="!guardandoRetorno"></i>
                            <i class="ph ph-spinner animate-spin mr-2" x-show="guardandoRetorno"></i>
                            <span x-text="guardandoRetorno ? 'Guardando...' : 'Confirmar Retorno'"></span>
                        </button>
                    </div>
                </form>
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
            retornos: @json($retornos ?? []),
            mostrarModalRetorno: false,
            guardandoRetorno: false,
            formRetorno: {
                proyecto_id: null,
                proyecto_nombre: '',
                disenador_nombre: '',
                articulo_id: null,
                articulo_nombre: '',
                destinatario: '',
                ubicacion_interna: '',
                persona_logistica: ''
            },
            articulosProyecto: [],
            
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
            },
            
            abrirModalRetorno(articulo) {
                this.formRetorno = {
                    proyecto_id: this.proyectoActual.id,
                    proyecto_nombre: this.proyectoActual.nombre,
                    disenador_nombre: this.proyectoActual.vendedor_nombre || 'No asignado',
                    articulo_id: articulo.id,
                    articulo_nombre: articulo.nombre,
                    destinatario: '',
                    ubicacion_interna: '',
                    persona_logistica: ''
                };
                this.mostrarModalRetorno = true;
            },

            async guardarRetorno() {
                this.guardandoRetorno = true;
                try {
                    const response = await fetch('{{ route("guardarRetorno") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.formRetorno)
                    });
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        alert('Retorno registrado correctamente.');
                        this.mostrarModalRetorno = false;
                        window.location.reload();
                    } else { alert('Error: ' + (data.message || 'Desconocido')); }
                } catch (error) {
                    console.error(error); alert('Error de conexión');
                } finally { this.guardandoRetorno = false; }
            }
        }
    }
</script>

@endsection
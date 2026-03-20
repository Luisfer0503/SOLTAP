@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="estatusProyectoApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-clock-counter-clockwise text-indigo-600 mr-2"></i> Historial y Estatus de Proyectos
        </h2>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar de Proyectos -->
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-100 shadow-sm shrink-0">
                <div class="relative">
                    <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="text" x-model="filtro" placeholder="Buscar proyecto o cliente..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
                <template x-for="proyecto in proyectosFiltrados" :key="proyecto.proyecto_id">
                    <button @click="seleccionarProyecto(proyecto)" 
                            class="w-full text-left p-4 border-b border-gray-100 hover:bg-indigo-50 transition focus:outline-none flex justify-between items-center"
                            :class="{ 'bg-indigo-100 border-l-4 border-l-indigo-600': proyectoSeleccionado && proyectoSeleccionado.proyecto_id === proyecto.proyecto_id }">
                        <div>
                            <p class="font-bold text-sm text-gray-800" x-text="proyecto.nombre_proyecto"></p>
                            <p class="text-xs text-gray-500" x-text="proyecto.cliente_nombre || 'Sin cliente'"></p>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </button>
                </template>
                <div x-show="proyectosFiltrados.length === 0" class="p-8 text-center text-gray-400 text-sm">
                    No se encontraron proyectos.
                </div>
            </div>
        </div>

        <!-- Área Principal -->
        <div class="w-2/3 bg-gray-50 flex flex-col h-full">
            <div x-show="!proyectoSeleccionado" class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <i class="ph ph-folder-open text-6xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium">Seleccione un proyecto para ver su historial.</p>
            </div>

            <div x-show="proyectoSeleccionado" class="flex-1 overflow-y-auto p-8" x-cloak>
                <div class="max-w-3xl mx-auto">
                    
                    <!-- Info del Proyecto -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6 flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900" x-text="proyectoSeleccionado?.nombre_proyecto"></h3>
                            <p class="text-gray-500 mt-1">Cliente: <span class="font-semibold text-gray-700" x-text="proyectoSeleccionado?.cliente_nombre"></span></p>
                        </div>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full border border-gray-200" x-text="proyectoSeleccionado?.estatus || 'Sin Estatus'"></span>
                    </div>

                    <!-- Formulario Nueva Interacción -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="ph ph-plus-circle text-indigo-500 mr-2"></i> Registrar Nueva Interacción
                        </h4>
                        <form @submit.prevent="guardarInteraccion" class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Interacción / Estatus</label>
                                <select x-model="form.interaccion_id" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50">
                                    <option value="">-- Seleccione una opción --</option>
                                    <template x-for="int in interacciones" :key="int.id || int.interaccion_id">
                                        <option :value="int.id || int.interaccion_id || int.nombre" x-text="int.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Comentarios / Observaciones</label>
                                <textarea x-model="form.comentarios" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50" placeholder="Escriba los detalles de la interacción..."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" :disabled="guardando" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center disabled:opacity-50">
                                    <i class="ph ph-floppy-disk mr-2" x-show="!guardando"></i>
                                    <i class="ph ph-spinner animate-spin mr-2" x-show="guardando"></i>
                                    <span x-text="guardando ? 'Guardando...' : 'Guardar Registro'"></span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Línea de Tiempo del Historial -->
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="ph ph-list-dashes text-gray-500 mr-2"></i> Historial de Movimientos
                    </h4>

                    <div x-show="cargandoHistorial" class="py-8 text-center text-gray-500 flex flex-col items-center">
                        <i class="ph ph-spinner animate-spin text-3xl mb-2"></i>
                        <span>Cargando historial...</span>
                    </div>

                    <div x-show="!cargandoHistorial && historial.length === 0" class="bg-white p-6 rounded-xl border border-gray-200 text-center text-gray-500 italic">
                        Aún no hay interacciones registradas para este proyecto.
                    </div>

                    <div x-show="!cargandoHistorial && historial.length > 0" class="space-y-4">
                        <template x-for="item in historial" :key="item.id">
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                                <div class="flex justify-between items-start mb-2 pl-2">
                                    <span class="font-bold text-gray-800 text-md" x-text="item.interaccion_nombre"></span>
                                    <span class="text-xs text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded" x-text="item.fecha_formateada"></span>
                                </div>
                                <p class="text-sm text-gray-600 pl-2 mt-2" x-show="item.comentarios" x-text="item.comentarios"></p>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function estatusProyectoApp() {
        return {
            proyectos: @json($proyectos),
            interacciones: @json($interacciones),
            filtro: '',
            proyectoSeleccionado: null,
            historial: [],
            cargandoHistorial: false,
            guardando: false,
            form: { interaccion_id: '', comentarios: '' },

            get proyectosFiltrados() {
                if (this.filtro === '') return this.proyectos;
                const busqueda = this.filtro.toLowerCase();
                return this.proyectos.filter(p => 
                    (p.nombre_proyecto && p.nombre_proyecto.toLowerCase().includes(busqueda)) ||
                    (p.cliente_nombre && p.cliente_nombre.toLowerCase().includes(busqueda))
                );
            },

            async seleccionarProyecto(proyecto) {
                this.proyectoSeleccionado = proyecto;
                this.form.interaccion_id = '';
                this.form.comentarios = '';
                this.cargarHistorial();
            },

            async cargarHistorial() {
                if (!this.proyectoSeleccionado) return;
                this.cargandoHistorial = true;
                try {
                    const response = await fetch(`{{ url('/erp/proyecto-interacciones') }}/${this.proyectoSeleccionado.proyecto_id}`);
                    this.historial = await response.json();
                } catch (error) {
                    console.error("Error al cargar historial", error);
                } finally {
                    this.cargandoHistorial = false;
                }
            },

            async guardarInteraccion() {
                if (!this.form.interaccion_id) return;
                
                this.guardando = true;
                const data = {
                    proyecto_id: this.proyectoSeleccionado.proyecto_id,
                    interaccion_id: this.form.interaccion_id,
                    comentarios: this.form.comentarios
                };

                try {
                    // Reutilizamos la ruta que ya existe para guardar interacciones
                    const response = await fetch('{{ route("guardarInteraccionProduccion") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        this.form.interaccion_id = '';
                        this.form.comentarios = '';
                        await this.cargarHistorial(); // Recargar historial automáticamente
                    } else {
                        alert('Error al guardar: ' + (result.message || 'Desconocido'));
                    }
                } catch (error) {
                    console.error("Error", error);
                    alert("Error de conexión");
                } finally {
                    this.guardando = false;
                }
            }
        }
    }
</script>
@endsection
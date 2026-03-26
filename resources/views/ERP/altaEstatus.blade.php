@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="altaEstatusApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-plus-circle text-indigo-600 mr-2"></i> Alta de Estatus y Logística
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-200">
            
            <div class="mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="ph ph-note-pencil text-indigo-500 mr-2"></i> Registrar Nueva Interacción y Logística
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Seleccione el proyecto para asignar un estatus y actualizar la información de logística.
                </p>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Seleccione el Proyecto</label>
                    <select x-model="proyecto_id" @change="seleccionarProyecto()" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50">
                        <option value="">-- Seleccionar Proyecto --</option>
                        <template x-for="p in proyectos" :key="p.proyecto_id">
                            <option :value="p.proyecto_id" x-text="`${p.nombre_proyecto} (${p.cliente_nombre || 'Sin Cliente'})`"></option>
                        </template>
                    </select>
                </div>

                <div x-show="proyecto_id" x-transition class="space-y-8 mt-6" style="display: none;">
                    <!-- Sección de Interacción -->
                    <form @submit.prevent="guardarInteraccion" class="bg-indigo-50 p-6 rounded-lg border border-indigo-100">
                        <h4 class="font-bold text-indigo-800 mb-4 flex items-center"><i class="ph ph-chat-circle mr-2"></i> Detalles de Interacción</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Estatus / Interacción</label>
                                <select x-model="formInt.interaccion_id" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                                    <option value="">-- Seleccionar Estatus --</option>
                                    @foreach($interacciones as $int)
                                        @php $idInt = $int->id ?? $int->interaccion_id; @endphp
                                        <option value="{{ $idInt }}">{{ $idInt }} - {{ $int->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Comentarios (Opcional)</label>
                                <textarea x-model="formInt.comentarios" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white" placeholder="Agregue observaciones o detalles de este movimiento..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4 mt-4 border-t border-indigo-200">
                            <button type="submit" :disabled="cargandoInt" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center disabled:opacity-50">
                                <i class="ph ph-floppy-disk mr-2" x-show="!cargandoInt"></i>
                                <i class="ph ph-spinner animate-spin mr-2" x-show="cargandoInt"></i>
                                <span x-text="cargandoInt ? 'Guardando...' : 'Guardar Interacción'"></span>
                            </button>
                        </div>
                    </form>

                    <!-- Sección de Logística -->
                    <form @submit.prevent="guardarLogistica" class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center"><i class="ph ph-truck mr-2 text-gray-600"></i> Información de Logística</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">Validación de Acceso</label>
                                <div class="flex gap-4 mb-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" x-model="formLog.es_planta_baja" value="si" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Sí, es Planta Baja</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" x-model="formLog.es_planta_baja" value="no" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">No (Requiere Escaleras...)</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Condiciones de Acceso / Excepciones</label>
                                    <textarea x-model="formLog.condiciones_acceso" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-white" rows="2" placeholder="Ej. Escaleras estrechas, horario restringido..."></textarea>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">Requerimientos de Entrega</label>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="formLog.requiere_emplaye" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Requiere Emplaye</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="formLog.requiere_desemplaye" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Requiere Desemplaye</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="formLog.requiere_instalacion" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Requiere Instalación</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="formLog.requiere_maniobraje" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Requiere Maniobraje</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 mt-4 border-t border-gray-200">
                            <button type="submit" :disabled="cargandoLog" class="px-6 py-2 bg-gray-800 text-white rounded-lg font-bold shadow hover:bg-gray-900 transition flex items-center disabled:opacity-50">
                                <i class="ph ph-floppy-disk mr-2" x-show="!cargandoLog"></i>
                                <i class="ph ph-spinner animate-spin mr-2" x-show="cargandoLog"></i>
                                <span x-text="cargandoLog ? 'Guardando...' : 'Guardar Logística'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function altaEstatusApp() {
        return {
            proyectos: @json($proyectos),
            proyecto_id: '', 
            cargandoInt: false,
            cargandoLog: false,
            formInt: {
                interaccion_id: '', 
                comentarios: '',
            },
            formLog: { 
                es_planta_baja: 'si',
                condiciones_acceso: '',
                requiere_instalacion: false,
                requiere_desemplaye: false,
                requiere_emplaye: false,
                requiere_maniobraje: false
            },
            seleccionarProyecto() {
                if (!this.proyecto_id) return;
                const proyecto = this.proyectos.find(p => p.proyecto_id == this.proyecto_id);
                if (proyecto) {
                    this.formLog.es_planta_baja = proyecto.es_planta_baja == 1 ? 'si' : 'no';
                    this.formLog.condiciones_acceso = proyecto.condiciones_acceso || '';
                    this.formLog.requiere_instalacion = proyecto.requiere_instalacion == 1;
                    this.formLog.requiere_desemplaye = proyecto.requiere_desemplaye == 1;
                    this.formLog.requiere_emplaye = proyecto.requiere_emplaye == 1;
                    this.formLog.requiere_maniobraje = proyecto.requiere_maniobraje == 1;
                }
            },
            async guardarInteraccion() {
                if (!this.proyecto_id || !this.formInt.interaccion_id) return;
                
                this.cargandoInt = true;
                try {
                    const payload = {
                        proyecto_id: this.proyecto_id,
                        interaccion_id: this.formInt.interaccion_id,
                        comentarios: this.formInt.comentarios
                    };
                    const response = await fetch('{{ route("guardarAltaEstatus") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        alert('Interacción guardada correctamente.');
                        this.formInt.interaccion_id = ''; 
                        this.formInt.comentarios = '';
                    } else { alert('Error: ' + (data.message || 'No autorizado para hacer esto.')); }
                } catch (error) { console.error(error); alert('Error de conexión.'); } 
                finally { this.cargandoInt = false; }
            },
            async guardarLogistica() {
                if (!this.proyecto_id) return;
                
                this.cargandoLog = true;
                try {
                    const payload = {
                        proyecto_id: this.proyecto_id,
                        ...this.formLog
                    };
                    const response = await fetch('{{ route("guardarLogisticaProyecto") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        alert('Información de logística guardada correctamente.');
                        
                        const idx = this.proyectos.findIndex(p => p.proyecto_id == this.proyecto_id);
                        if (idx !== -1) {
                            this.proyectos[idx].es_planta_baja = this.formLog.es_planta_baja === 'si' ? 1 : 0;
                            this.proyectos[idx].condiciones_acceso = this.formLog.condiciones_acceso;
                            this.proyectos[idx].requiere_instalacion = this.formLog.requiere_instalacion ? 1 : 0;
                            this.proyectos[idx].requiere_desemplaye = this.formLog.requiere_desemplaye ? 1 : 0;
                            this.proyectos[idx].requiere_emplaye = this.formLog.requiere_emplaye ? 1 : 0;
                            this.proyectos[idx].requiere_maniobraje = this.formLog.requiere_maniobraje ? 1 : 0;
                        }
                    } else { alert('Error: ' + (data.message || 'Error al guardar logística.')); }
                } catch (error) { console.error(error); alert('Error de conexión.'); } 
                finally { this.cargandoLog = false; }
            }
        }
    }
</script>
@endsection
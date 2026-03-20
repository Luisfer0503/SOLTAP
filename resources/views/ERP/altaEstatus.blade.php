@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="altaEstatusApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-plus-circle text-indigo-600 mr-2"></i> Alta de Estatus
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-200">
            
            <div class="mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="ph ph-note-pencil text-indigo-500 mr-2"></i> Registrar Nueva Interacción (Ventas)
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Seleccione el proyecto y asigne el estatus. Tenga en cuenta que solo los roles autorizados pueden hacer esto y las interacciones están limitadas (Id 2 al 7).
                </p>
            </div>
            
            <form @submit.prevent="guardarInteraccion" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Seleccione el Proyecto</label>
                    <select x-model="form.proyecto_id" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50">
                        <option value="">-- Seleccionar Proyecto --</option>
                        @foreach($proyectos as $p)
                            <option value="{{ $p->proyecto_id }}">{{ $p->nombre_proyecto }} ({{ $p->cliente_nombre }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Estatus / Interacción</label>
                    <select x-model="form.interaccion_id" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50">
                        <option value="">-- Seleccionar Estatus --</option>
                        @foreach($interacciones as $int)
                            @php $idInt = $int->id ?? $int->interaccion_id; @endphp
                            <option value="{{ $idInt }}">{{ $idInt }} - {{ $int->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Comentarios (Opcional)</label>
                    <textarea x-model="form.comentarios" rows="4" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50" placeholder="Agregue observaciones o detalles de este movimiento..."></textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" :disabled="cargando" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center disabled:opacity-50">
                        <i class="ph ph-floppy-disk mr-2" x-show="!cargando"></i>
                        <i class="ph ph-spinner animate-spin mr-2" x-show="cargando"></i>
                        <span x-text="cargando ? 'Guardando...' : 'Guardar Estatus'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function altaEstatusApp() {
        return {
            cargando: false,
            form: { proyecto_id: '', interaccion_id: '', comentarios: '' },
            async guardarInteraccion() {
                if (!this.form.proyecto_id || !this.form.interaccion_id) return;
                
                this.cargando = true;
                try {
                    const response = await fetch('{{ route("guardarAltaEstatus") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        alert('Estatus registrado correctamente en el historial.');
                        this.form.proyecto_id = ''; this.form.interaccion_id = ''; this.form.comentarios = '';
                    } else { alert('Error: ' + (data.message || 'No autorizado para hacer esto.')); }
                } catch (error) { console.error(error); alert('Error de conexión.'); } 
                finally { this.cargando = false; }
            }
        }
    }
</script>
@endsection
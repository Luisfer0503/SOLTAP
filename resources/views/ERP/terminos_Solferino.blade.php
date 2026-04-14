@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="terminosApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-file-text text-indigo-600 mr-2"></i> Gestión de Términos y Condiciones
        </h2>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar de Términos -->
        <div class="w-1/4 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-4 border-b border-gray-100 bg-gray-50 font-bold text-gray-600 text-sm uppercase tracking-wider">
                Lista de Términos
            </div>
            <template x-for="termino in terminos" :key="termino.termino_id">
                <button @click="seleccionarTermino(termino)" 
                        class="w-full text-left p-4 border-b border-gray-100 hover:bg-indigo-50 transition focus:outline-none flex justify-between items-center"
                        :class="{ 'bg-indigo-100 border-l-4 border-l-indigo-600': terminoSeleccionado && terminoSeleccionado.termino_id === termino.termino_id }">
                    <span class="font-bold text-sm text-gray-800" x-text="termino.nombre"></span>
                    <i class="ph ph-caret-right text-gray-400"></i>
                </button>
            </template>
        </div>

        <!-- Área de Edición -->
        <div class="w-3/4 bg-gray-50 flex flex-col h-full">
            <div x-show="!terminoSeleccionado" class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <i class="ph ph-file-text text-6xl mb-4 text-gray-300"></i>
                <p class="text-lg font-medium">Seleccione un registro para editar su contenido.</p>
            </div>

            <div x-show="terminoSeleccionado" class="flex-1 overflow-y-auto p-8" x-cloak>
                <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
                    <div class="mb-4 flex justify-between items-center border-b border-gray-100 pb-4">
                        <h3 class="text-xl font-bold text-gray-900" x-text="terminoSeleccionado?.nombre"></h3>
                        <button @click="guardarCambios()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center" :disabled="guardando">
                            <i class="ph ph-floppy-disk mr-2" x-show="!guardando"></i>
                            <i class="ph ph-spinner animate-spin mr-2" x-show="guardando"></i>
                            <span x-text="guardando ? 'Guardando...' : 'Guardar Cambios'"></span>
                        </button>
                    </div>
                    
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contenido de las condiciones:</label>
                        <textarea x-model="terminoSeleccionado.contenido" class="w-full h-96 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm p-4" placeholder="Escriba el contenido aquí..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function terminosApp() {
        return {
            terminos: @json($terminos ?? []),
            terminoSeleccionado: null,
            guardando: false,
            seleccionarTermino(termino) {
                this.terminoSeleccionado = termino;
            },
            async guardarCambios() {
                if (!this.terminoSeleccionado) return;
                this.guardando = true;
                try {
                    const response = await fetch('{{ route("terminos.guardar") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ terminos: this.terminos }) });
                    const result = await response.json();
                    if (result.success) { alert('Contenido guardado correctamente.'); } else { alert('Error al guardar: ' + (result.message || 'Desconocido')); }
                } catch (error) { console.error(error); alert('Error de conexión.'); } finally { this.guardando = false; }
            }
        }
    }
</script>
@endsection
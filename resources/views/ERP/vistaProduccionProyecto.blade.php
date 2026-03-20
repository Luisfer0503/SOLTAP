@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="produccionScannerApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-qr-code text-indigo-600 mr-2"></i> Detalles de Producción del Proyecto
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Proyecto Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $proyecto->nombre }}</h3>
                        <p class="text-gray-500">Cliente: <span class="font-semibold text-gray-700">{{ $proyecto->cliente_nombre }}</span></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        {{ $proyecto->estatus ?? 'En Producción' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mt-4 border-t border-gray-100 pt-4">
                    <div>
                        <span class="block font-bold text-gray-400 uppercase text-[10px] mb-1">Teléfono de Contacto</span>
                        {{ $proyecto->telefono ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="block font-bold text-gray-400 uppercase text-[10px] mb-1">Correo Electrónico</span>
                        {{ $proyecto->correo ?? 'N/A' }}
                    </div>
                    <div class="col-span-2">
                        <span class="block font-bold text-gray-400 uppercase text-[10px] mb-1">Dirección de Entrega</span>
                        {{ $proyecto->direccion ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Interacciones / Formularios -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="ph ph-chat-circle text-indigo-500 mr-2"></i> Registrar Interacción / Actualizar Estatus
                </h4>
                
                <form @submit.prevent="guardarInteraccion" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Seleccione una acción o interacción</label>
                        <select x-model="form.interaccion_id" class="w-full border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                            <option value="">-- Seleccione un motivo --</option>
                            @foreach($interacciones as $int)
                                <option value="{{ $int->id ?? $int->interaccion_id ?? $int->nombre }}">{{ $int->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Comentarios Adicionales (Opcional)</label>
                        <textarea x-model="form.comentarios" rows="3" class="w-full border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Añadir observaciones sobre la producción..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center" :disabled="cargando">
                            <i class="ph ph-floppy-disk mr-2" x-show="!cargando"></i>
                            <span x-show="!cargando">Guardar Interacción</span>
                            <span x-show="cargando"><i class="ph ph-spinner animate-spin mr-2"></i>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Historial de Interacciones -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="ph ph-clock-counter-clockwise text-indigo-500 mr-2"></i> Historial de Interacciones
                </h4>
                @if(count($historial ?? []) > 0)
                    <div class="space-y-4">
                        @foreach($historial as $h)
                            <div class="flex border-l-2 border-indigo-200 pl-4 py-1 flex-col relative">
                                <div class="absolute w-2.5 h-2.5 bg-indigo-500 rounded-full -left-[6px] top-1.5 border-2 border-white"></div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-gray-800 text-sm">{{ $h->interaccion_nombre }}</span>
                                    <span class="text-xs text-gray-500 font-mono"><i class="ph ph-calendar-blank mr-1"></i>{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y h:i A') }}</span>
                                </div>
                                @if($h->comentarios)
                                    <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 mt-1">{{ $h->comentarios }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">No hay interacciones registradas para este proyecto.</p>
                @endif
            </div>

            <!-- Artículos en Producción -->
            <h4 class="text-lg font-bold text-gray-800 mb-4"><i class="ph ph-package mr-2"></i> Artículos Relacionados</h4>
            <div class="space-y-4">
                @foreach($articulos as $art)
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex gap-4">
                        <div class="w-24 h-24 bg-gray-50 rounded flex items-center justify-center border border-gray-200 overflow-hidden shrink-0">
                            @if($art->imagen)
                                <img src="{{ asset('storage/' . $art->imagen) }}" class="w-full h-full object-cover">
                            @else
                                <i class="ph ph-image text-3xl text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-gray-800 text-lg">{{ $art->nombre }}</h5>
                                    <p class="text-xs text-gray-500 font-mono mt-1">ID: {{ $art->articulo_produccion_id ?? 'N/A' }}</p>
                                </div>
                                <span class="bg-indigo-50 text-indigo-700 font-black px-4 py-1.5 rounded-lg text-lg">x{{ $art->cantidad }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-2 rounded border border-gray-100">
                                {{ $art->descripcion ?? 'Sin descripción adicional.' }}
                            </p>
                            <div class="mt-2 text-xs text-gray-500 font-bold flex gap-4">
                                <span><i class="ph ph-rulers mr-1"></i>{{ $art->alto }}x{{ $art->ancho }}x{{ $art->profundo }} cm</span>
                                <span><i class="ph ph-scales mr-1"></i>{{ $art->peso }} kg</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</main>

<script>
    function produccionScannerApp() {
        return {
            cargando: false,
            form: {
                proyecto_id: '{{ $proyecto->proyecto_id }}',
                interaccion_id: '',
                comentarios: ''
            },
            async guardarInteraccion() {
                if (!this.form.interaccion_id) {
                    alert('Por favor seleccione una interacción.');
                    return;
                }
                this.cargando = true;
                try {
                    const response = await fetch('{{ route("guardarInteraccionProduccion") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Interacción registrada correctamente.');
                        this.form.interaccion_id = '';
                        this.form.comentarios = '';
                        // Recargar la página para mostrar el nuevo registro en el historial
                        window.location.reload();
                    } else {
                        alert('Error al guardar: ' + (data.message || 'Error desconocido'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Ocurrió un error de conexión');
                } finally {
                    this.cargando = false;
                }
            }
        }
    }
</script>
@endsection
@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="{ imageModalOpen: false, modalImageUrl: '' }">

    <!-- Encabezado del Proyecto -->
    <header class="bg-white border-b px-8 py-5 shadow-sm z-10 flex justify-between items-center">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('seguimientoProyectos') }}" class="text-gray-400 hover:text-blue-600 transition">
                    <i class="ph ph-arrow-left text-xl"></i>
                </a>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    {{ $proyecto->nombre }}
                </h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                    {{ $proyecto->estatus ?? 'En Proceso' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 ml-8">
                <i class="ph ph-user mr-1"></i> Cliente: <span class="font-semibold text-gray-700">{{ $proyecto->cliente_nombre }}</span>
                <span class="mx-2">|</span>
                <i class="ph ph-package mr-1"></i> Total Artículos: <span class="font-semibold text-gray-700">{{ count($articulos) }}</span>
            </p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm text-sm font-bold flex items-center">
                <i class="ph ph-printer mr-2"></i> Imprimir Ficha
            </button>
        </div>
    </header>

    <!-- Contenido Scrollable -->
    <div class="flex-1 overflow-y-auto p-8">
        
        <!-- Detalles de Logística del Proyecto (Global) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 max-w-5xl mx-auto">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">
                Logística del Proyecto
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="block text-[10px] uppercase text-gray-400 font-bold">Es Planta Baja</span>
                    <span class="text-sm font-semibold {{ $proyecto->es_planta_baja == 1 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $proyecto->es_planta_baja == 1 ? 'Sí' : 'No (Requiere Escaleras...)' }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase text-gray-400 font-bold">Condiciones de Acceso</span>
                    <p class="text-sm text-gray-700">
                        {{ $proyecto->condiciones_acceso ?: 'Sin condiciones especiales.' }}
                    </p>
                </div>
                {{-- You can add other project-level logistics details here if needed,
                     e.g., requiere_instalacion, requiere_desemplaye, etc.,
                     by fetching them in the ERPController.php's detalleProyecto method. --}}
            </div>
        </div>

        @if(count($articulos) == 0)
            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                <i class="ph ph-package-open text-6xl mb-4"></i>
                <p class="text-lg font-medium">No hay artículos registrados en este proyecto.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 max-w-5xl mx-auto">
                @foreach($articulos as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
                    
                    <!-- Imagen del Artículo -->
                    <div class="w-full md:w-64 h-64 md:h-auto bg-gray-100 flex-shrink-0 relative border-r border-gray-100">
                        @if($item->imagen)
                            <img src="{{ asset('storage/' . $item->imagen) }}" class="w-full h-full object-cover">
                            <div class="absolute bottom-2 right-2">
                                <button type="button" @click="imageModalOpen = true; modalImageUrl = '{{ asset('storage/' . $item->imagen) }}'" class="bg-white p-2 rounded-full shadow hover:text-blue-600 transition focus:outline-none">
                                    <i class="ph ph-magnifying-glass-plus"></i>
                                </button>
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <i class="ph ph-image text-4xl mb-2"></i>
                                <span class="text-xs">Sin imagen</span>
                            </div>
                        @endif
                    </div>

                    <!-- Detalles -->
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $item->nombre }}</h3>
                                <p class="text-xs text-gray-500 font-mono mt-1">ID: {{ $item->articulo_produccion_id ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-2xl font-bold text-blue-600">{{ $item->cantidad }} <span class="text-sm font-normal text-gray-500">pza(s)</span></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div>
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">Dimensiones</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $item->alto }} x {{ $item->ancho }} x {{ $item->profundo }} m</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">Peso / Vol</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $item->peso }} kg / {{ $item->cubicaje }} m³</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase text-gray-400 font-bold">División</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $item->tiene_division ? 'Sí (' . $item->piezas_divididas . ' pzs)' : 'No' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Descripción Técnica</h4>
                            <p class="text-sm text-gray-600 leading-relaxed bg-white border border-gray-100 p-3 rounded">
                                {{ $item->descripcion ?? 'Sin descripción detallada.' }}
                            </p>
                        </div>

                        <div class="mt-auto">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Materiales y Acabados</h4>
                            <div class="flex flex-wrap gap-3">
                                @forelse($item->materiales as $mat)
                                    <div class="flex items-center p-1.5 pr-3 rounded-lg border border-gray-200 bg-gray-50 text-xs shadow-sm">
                                        @if($mat->material_imagen)
                                            <img src="{{ asset('storage/' . $mat->material_imagen) }}" alt="{{ $mat->tipo_material }}" class="w-8 h-8 rounded object-cover mr-2 border border-gray-300 cursor-pointer hover:opacity-75 transition-opacity" @click="imageModalOpen = true; modalImageUrl = '{{ asset('storage/' . $mat->material_imagen) }}'">
                                        @else
                                            <div class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center mr-2 text-gray-400">
                                                <i class="ph ph-palette text-lg"></i>
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-700 leading-tight">{{ $mat->tipo_material }}</span>
                                            <span class="text-gray-500 text-[10px] leading-tight mt-0.5">
                                                {{ $mat->material_nombre ? $mat->material_nombre . ($mat->material_color ? ' - ' . $mat->material_color : '') : ($mat->custom_descripcion ?: 'Sin especificar') }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-xs text-gray-400 italic">No se especificaron materiales.</span>
                                @endforelse
                            </div>
                        </div>

                        @if($item->pdf_archivo)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="{{ asset('storage/' . $item->pdf_archivo) }}" target="_blank" class="inline-flex items-center text-sm font-bold text-red-600 hover:text-red-800 transition">
                                <i class="ph ph-file-pdf mr-2 text-lg"></i> Ver Plano / Guía Adjunta
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Visor de Imágenes -->
    <div x-show="imageModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-80 p-4" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative max-w-5xl max-h-full flex justify-center items-center" @click.away="imageModalOpen = false">
            <button @click="imageModalOpen = false" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl focus:outline-none" title="Cerrar">
                &times;
            </button>
            <img :src="modalImageUrl" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl bg-white p-2 border border-gray-200">
        </div>
    </div>

</main>
@endsection
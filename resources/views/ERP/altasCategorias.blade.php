@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100" x-data="catalogoApp()">
    
    <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-tag text-blue-600 mr-2"></i> Gestión de Catálogo
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-6xl mx-auto">
            
            <!-- Mensajes de sesión (Flash) -->
            @if(session('mensaje'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                    <i class="ph ph-check-circle text-xl mr-2"></i> {{ session('mensaje') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Columna Izquierda: Alta de Categorías -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-4">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-squares-four mr-2 text-indigo-500"></i> Nueva Categoría
                            </h3>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="guardarCategoria">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Categoría</label>
                                    <input type="text" x-model="catForm.nombre" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej. Oficina, Cocina...">
                                </div>
                                <button type="submit" :disabled="loadingCat" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition flex justify-center items-center disabled:opacity-50">
                                    <span x-show="!loadingCat">Guardar Categoría</span>
                                    <span x-show="loadingCat"><i class="ph ph-spinner animate-spin"></i> Guardando...</span>
                                </button>
                                <!-- Mensaje éxito local -->
                                <p x-show="catMessage" x-text="catMessage" class="mt-2 text-xs text-green-600 font-bold text-center"></p>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Alta de Artículos -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-package mr-2 text-blue-500"></i> Nuevo Artículo
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('guardarArticulo') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Artículo</label>
                                        <input type="text" name="nombre" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. Escritorio Ejecutivo L">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                        <div class="relative">
                                            <select name="categoria_id" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                                <option value="">-- Seleccionar --</option>
                                                <template x-for="cat in categorias" :key="cat.categoria_id">
                                                    <option :value="cat.categoria_id" x-text="cat.nombre"></option>
                                                </template>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                <i class="ph ph-caret-down"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Si creaste una categoría nueva, ya aparece aquí.</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow transition flex items-center">
                                        <i class="ph ph-floppy-disk mr-2"></i> Guardar Artículo
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
    function catalogoApp() {
        return {
            categorias: @json($categorias),
            catForm: { nombre: '', descripcion: '' },
            loadingCat: false,
            catMessage: '',

            async guardarCategoria() {
                this.loadingCat = true;
                this.catMessage = '';
                try {
                    const response = await fetch("{{ route('guardarCategoria') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.catForm)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Agregar la nueva categoría a la lista localmente
                        this.categorias.push(data.categoria);
                        // Ordenar alfabéticamente (opcional)
                        this.categorias.sort((a, b) => a.nombre.localeCompare(b.nombre));
                        
                        this.catMessage = '¡Categoría agregada!';
                        this.catForm.nombre = '';
                        this.catForm.descripcion = '';
                        
                        // Limpiar mensaje después de 3 seg
                        setTimeout(() => this.catMessage = '', 3000);
                    } else {
                        alert('Error: ' + (data.error || 'No se pudo guardar'));
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión');
                } finally {
                    this.loadingCat = false;
                }
            }
        }
    }
</script>

@stop
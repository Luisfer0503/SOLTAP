@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="cotizadorApp()">
    <!-- Header -->
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-currency-dollar text-green-600 mr-2"></i> Asignación de Precios y Cotizaciones
        </h2>
    </header>

    <!-- Listado de Proyectos -->
    <div class="flex-1 overflow-y-auto p-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Vendedor</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($proyectos as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $p->nombre_proyecto }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $p->cliente_nombre }}</div>
                            <div class="text-xs text-gray-500">{{ $p->direccion }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $p->telefono }}</div>
                            <div class="text-xs text-gray-500">{{ $p->correo }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $p->vendedor_nombre }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="abrirCotizador({{ json_encode($p) }})" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-sm flex items-center ml-auto">
                                <i class="ph ph-calculator mr-2"></i> Cotizar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Cotizador -->
    <div x-show="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
        <div class="bg-white w-full max-w-6xl h-[90vh] rounded-xl shadow-2xl flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-bold flex items-center"><i class="ph ph-file-text mr-2"></i> Cotización de Proyecto</h3>
                    <p class="text-sm text-gray-300" x-text="proyecto?.nombre_proyecto"></p>
                </div>
                <button @click="cerrarModal()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Info Proyecto -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 grid grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Cliente</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.cliente_nombre"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Teléfono</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.telefono"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Correo</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.correo"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500 text-xs font-bold uppercase">Fecha</span>
                        <span class="font-semibold text-gray-800" x-text="formatDate(proyecto?.fecha)"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500 text-xs font-bold uppercase">Dirección</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.direccion"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500 text-xs font-bold uppercase">Vendedor</span>
                        <span class="font-semibold text-gray-800" x-text="proyecto?.vendedor_nombre"></span>
                    </div>
                </div>

                <!-- Tabla Artículos -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Cant.</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Dimensiones / Peso</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in articulos" :key="index">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 flex-shrink-0 bg-gray-100 rounded overflow-hidden mr-3 border border-gray-200">
                                                <template x-if="item.imagen">
                                                    <img :src="item.imagen" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!item.imagen">
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400"><i class="ph ph-image"></i></div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900" x-text="item.nombre"></div>
                                                <div class="text-xs text-gray-500" x-text="item.id_articulo_produccion"></div>
                                                <div class="text-xs text-gray-400 truncate w-48" x-text="item.descripcion"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-800" x-text="item.cantidad"></td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-600">
                                        <div x-text="`${item.alto}x${item.ancho}x${item.profundo}m`"></div>
                                        <div x-text="`${item.peso}kg`"></div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number" x-model="item.precio_unitario" class="w-24 text-right text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900" x-text="money(item.cantidad * (item.precio_unitario || 0))"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Totales -->
                <div class="flex justify-end">
                    <div class="w-80 bg-white p-4 rounded-lg shadow-sm border border-gray-200 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal Artículos:</span>
                            <span class="font-bold text-gray-900" x-text="money(subtotalArticulos)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Envío:</span>
                            <input type="number" x-model="costoEnvio" class="w-24 text-right text-sm border-gray-300 rounded py-1" placeholder="0">
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Descuento:</span>
                            <input type="number" x-model="descuento" class="w-24 text-right text-sm border-gray-300 rounded py-1" placeholder="0">
                        </div>
                        <div class="border-t border-gray-100 pt-2 flex justify-between text-sm">
                            <span class="text-gray-600 font-bold">Subtotal:</span>
                            <span class="font-bold text-gray-900" x-text="money(subtotalGeneral)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">IVA (16%):</span>
                            <span class="font-bold text-gray-900" x-text="money(iva)"></span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg">
                            <span class="font-bold text-gray-800">Total a Pagar:</span>
                            <span class="font-bold text-blue-600" x-text="money(totalPagar)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                <button @click="cerrarModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition">Cancelar</button>
                <button @click="imprimirPdf()" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg flex items-center">
                    <i class="ph ph-file-pdf mr-2"></i> Generar PDF
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    function cotizadorApp() {
        return {
            mostrarModal: false,
            proyecto: null,
            articulos: [],
            costoEnvio: 0,
            descuento: 0,

            async abrirCotizador(proyecto) {
                this.proyecto = proyecto;
                this.articulos = [];
                this.costoEnvio = 0;
                this.descuento = 0;
                this.mostrarModal = true;
                
                // Cargar artículos
                try {
                    const response = await fetch(`{{ url('/erp/articulos-proyecto') }}/${proyecto.proyecto_id}`);
                    const data = await response.json();
                    this.articulos = data.map(item => ({
                        ...item,
                        precio_unitario: 0 // Inicializar precio
                    }));
                } catch (error) {
                    console.error('Error cargando artículos:', error);
                    alert('No se pudieron cargar los artículos del proyecto.');
                }
            },

            cerrarModal() {
                this.mostrarModal = false;
                this.proyecto = null;
            },

            get subtotalArticulos() {
                return this.articulos.reduce((sum, item) => sum + (item.cantidad * (parseFloat(item.precio_unitario) || 0)), 0);
            },

            get subtotalGeneral() {
                return Math.max(0, this.subtotalArticulos + (parseFloat(this.costoEnvio) || 0) - (parseFloat(this.descuento) || 0));
            },

            get iva() {
                return this.subtotalGeneral * 0.16;
            },

            get totalPagar() {
                return this.subtotalGeneral + this.iva;
            },

            money(value) {
                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('es-MX');
            },

            async imprimirPdf() {
                const data = {
                    proyecto: this.proyecto,
                    articulos: this.articulos,
                    totales: {
                        subtotal_articulos: this.subtotalArticulos,
                        envio: this.costoEnvio,
                        descuento: this.descuento,
                        subtotal: this.subtotalGeneral,
                        iva: this.iva,
                        total: this.totalPagar
                    }
                };

                // Enviar a backend para generar PDF
                try {
                    const response = await fetch('{{ route("generarCotizacionPdf") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        // Aquí podrías manejar la descarga del blob si el backend retornara el PDF
                        // Por ahora, simulamos éxito
                        alert('Solicitud de PDF enviada. (Funcionalidad de descarga pendiente de backend)');
                    } else {
                        alert('Error al generar PDF');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión');
                }
            }
        }
    }
</script>

@endsection
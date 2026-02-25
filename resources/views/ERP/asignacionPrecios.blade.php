@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="pricingApp({{ json_encode($articulos) }})">
    
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 sticky top-0">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-currency-dollar text-green-600 mr-2"></i> Asignación de Precios
            </h2>
            
            <div class="flex items-center bg-gray-100 rounded-lg px-4 py-2 border border-gray-300 w-80">
                <i class="ph ph-magnifying-glass text-gray-500 mr-2"></i>
                <input type="text" x-model="searchTerm" placeholder="Buscar por nombre o ID..." 
                       class="bg-transparent text-sm text-gray-700 placeholder-gray-500 focus:outline-none w-full">
            </div>
            
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <span class="text-xs font-semibold text-gray-500 px-3">Aplicar Margen Global:</span>
                <button @click="aplicarMargen(30)" class="px-3 py-1 text-xs font-bold text-gray-600 hover:bg-white hover:shadow rounded transition">30%</button>
                <button @click="aplicarMargen(50)" class="px-3 py-1 text-xs font-bold text-gray-600 hover:bg-white hover:shadow rounded transition">50%</button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-500 uppercase font-bold">Costo Producción (Base)</p>
                <p class="text-lg font-bold text-gray-700" x-text="money(totalCosto)"></p>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                <p class="text-xs text-blue-600 uppercase font-bold">Precio Venta Total</p>
                <p class="text-2xl font-bold text-blue-700" x-text="money(totalVenta)"></p>
            </div>

            <div class="rounded-lg p-3 border transition-colors duration-300" 
                 :class="margenGlobal < 20 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'">
                <div class="flex justify-between items-center">
                    <p class="text-xs uppercase font-bold" 
                       :class="margenGlobal < 20 ? 'text-red-600' : 'text-green-600'">Utilidad / Margen</p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                          :class="margenGlobal < 20 ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800'">
                        <span x-text="margenGlobal"></span>%
                    </span>
                </div>
                <p class="text-xl font-bold" 
                   :class="margenGlobal < 20 ? 'text-red-700' : 'text-green-700'" 
                   x-text="money(totalUtilidad)"></p>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-6xl mx-auto space-y-4">
            
            <template x-for="(item, index) in filteredItems" :key="item.id">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center transition hover:shadow-md">
                    
                    <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 relative group cursor-pointer">
                        <template x-if="item.imagen">
                            <img :src="item.imagen" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.imagen">
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class="ph ph-image text-3xl"></i>
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                            <i class="ph ph-magnifying-glass-plus text-white opacity-0 group-hover:opacity-100"></i>
                        </div>
                    </div>

                    <div class="ml-6 flex-1">
                        <div class="flex items-center mb-1">
                            <h3 class="text-lg font-bold text-gray-800" x-text="item.nombre"></h3>
                            <span class="ml-3 text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded border border-gray-200" x-text="item.dimensiones"></span>
                        </div>
                        <p class="text-xs text-gray-400 mb-3">ID: <span x-text="item.id"></span></p>
                        
                        <div class="flex items-center text-xs space-x-4">
                            <div>
                                <span class="text-gray-500 block">Costo Producción (Materiales)</span>
                                <span class="font-bold text-gray-700" x-text="money(item.costo_produccion)"></span>
                            </div>
                            <i class="ph ph-arrow-right text-gray-300"></i>
                            <div class="relative">
                                <span class="text-blue-600 font-bold block mb-1">Precio Final Cliente</span>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" x-model.number="item.precio_venta" 
                                           class="w-40 pl-7 pr-3 py-1.5 rounded-lg border-2 border-blue-100 focus:border-blue-500 focus:ring-blue-500 font-bold text-gray-800 shadow-sm text-lg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-32 text-right border-l pl-6 border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Margen</p>
                        <div class="flex items-center justify-end space-x-2">
                            <span class="text-2xl font-bold" 
                                  :class="calcularMargen(item) < 20 ? 'text-red-500' : 'text-green-500'"
                                  x-text="calcularMargen(item) + '%'"></span>
                            
                            <template x-if="calcularMargen(item) >= 20">
                                <i class="ph ph-trend-up text-green-500 text-xl"></i>
                            </template>
                            <template x-if="calcularMargen(item) < 20">
                                <i class="ph ph-warning-circle text-red-500 text-xl" title="Margen bajo"></i>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Ganancia: <span x-text="money(item.precio_venta - item.costo_produccion)"></span></p>
                    </div>

                </div>
            </template>

        </div>
    </div>

    <div class="bg-white border-t p-4 flex justify-end space-x-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
        <button class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
            Cancelar
        </button>
        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg hover:bg-blue-700 flex items-center">
            <i class="ph ph-file-pdf mr-2 text-lg"></i> Confirmar y Generar Cotización
        </button>
    </div>

</main>

<script>
    function pricingApp(data) {
        return {
            items: data,
            searchTerm: '',

            // Filtrar items según búsqueda
            get filteredItems() {
                if (!this.searchTerm.trim()) {
                    return this.items;
                }
                const term = this.searchTerm.toLowerCase();
                return this.items.filter(item => 
                    item.nombre.toLowerCase().includes(term) || 
                    (item.id && item.id.toString().toLowerCase().includes(term))
                );
            },

            // Cálculos Globales (Computed Properties)
            get totalCosto() {
                return this.filteredItems.reduce((sum, item) => sum + parseFloat(item.costo_produccion), 0);
            },
            get totalVenta() {
                return this.filteredItems.reduce((sum, item) => sum + parseFloat(item.precio_venta || 0), 0);
            },
            get totalUtilidad() {
                return this.totalVenta - this.totalCosto;
            },
            get margenGlobal() {
                if (this.totalVenta === 0) return 0;
                return Math.round(((this.totalVenta - this.totalCosto) / this.totalVenta) * 100);
            },

            // Funciones por Item
            calcularMargen(item) {
                if (!item.precio_venta || item.precio_venta == 0) return 0;
                let costo = parseFloat(item.costo_produccion);
                let precio = parseFloat(item.precio_venta);
                // Fórmula de Margen: ((Precio - Costo) / Precio) * 100
                return Math.round(((precio - costo) / precio) * 100);
            },

            // Herramienta Masiva
            aplicarMargen(porcentaje) {
                // Si quiero ganar el 30%, la fórmula es: Costo / (1 - 0.30)
                let factor = 1 - (porcentaje / 100);
                this.filteredItems.forEach(item => {
                    item.precio_venta = Math.ceil(item.costo_produccion / factor);
                });
            },

            // Formato de Dinero
            money(value) {
                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
            }
        }
    }
</script>

@endsection
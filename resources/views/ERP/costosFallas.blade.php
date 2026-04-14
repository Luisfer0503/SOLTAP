@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100" x-data="costosFallasApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-currency-dollar text-green-600 mr-2"></i> Asignación de Costos a Materiales de Fallas
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-7xl mx-auto">
            <div x-show="fallas.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-lg shadow-sm border">
                <i class="ph ph-check-circle text-4xl text-green-500 mb-3"></i>
                <p class="font-bold">¡Todo en orden!</p>
                <p class="text-sm">No hay materiales pendientes de costeo en los reportes de fallas.</p>
            </div>

            <div class="space-y-6" x-show="fallas.length > 0">
                <template x-for="(falla, index) in fallas" :key="falla.falla_id">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800" x-text="`Falla #${falla.falla_id} - ${falla.articulo_nombre}`"></h3>
                                <p class="text-xs text-gray-500" x-text="`Proyecto: ${falla.proyecto_nombre} | Fecha: ${new Date(falla.fecha).toLocaleDateString()}`"></p>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="guardarCostos(falla)">
                                <table class="min-w-full">
                                    <thead class="text-xs text-gray-500 uppercase">
                                        <tr>
                                            <th class="pb-2 text-left font-bold">Material</th>
                                            <th class="pb-2 text-right font-bold w-48">Asignar Costo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(mat, matIndex) in falla.materiales_pendientes" :key="matIndex">
                                            <tr class="border-t">
                                                <td class="py-3 text-sm text-gray-700">
                                                    <template x-if="mat.isNew">
                                                        <input type="text" x-model="mat.material" required class="w-full text-sm border-gray-300 rounded focus:ring-green-500 focus:border-green-500 uppercase" placeholder="Nombre del material">
                                                    </template>
                                                    <template x-if="!mat.isNew">
                                                        <span x-text="mat.material"></span>
                                                    </template>
                                                </td>
                                                <td class="py-3">
                                                    <div class="relative flex justify-end">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                                                        <input type="number" step="0.01" x-model="mat.costo" required class="w-32 pl-6 text-right text-sm font-bold border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="0.00">
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <div class="flex justify-between items-center mt-4">
                                    <button type="button" @click="agregarMaterial(falla)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition flex items-center text-sm">
                                        <i class="ph ph-plus mr-2"></i> Agregar Material
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-md flex items-center" :disabled="falla.guardando">
                                        <i class="ph ph-floppy-disk mr-2" x-show="!falla.guardando"></i>
                                        <i class="ph ph-spinner animate-spin mr-2" x-show="falla.guardando"></i>
                                        <span x-text="falla.guardando ? 'Guardando...' : 'Guardar Costos'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</main>

<script>
    function costosFallasApp() {
        return {
            fallas: Object.values(@json($fallas) || {}).map(f => ({...f, guardando: false})),

            agregarMaterial(falla) {
                falla.materiales_pendientes.push({
                    material: '',
                    costo: '',
                    isNew: true
                });
            },
            
            async guardarCostos(falla) {
                falla.guardando = true;

                const payload = {
                    falla_id: falla.falla_id,
                    materiales: falla.materiales_pendientes.map(m => ({ material: m.material, costo: m.costo }))
                };

                try {
                    const response = await fetch("{{ route('guardarCostosFalla') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert(result.message);
                        // Remove the item from the list as it's now costed
                        this.fallas = this.fallas.filter(f => f.falla_id !== falla.falla_id);
                    } else {
                        alert('Error: ' + (result.message || 'No se pudieron guardar los costos.'));
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión.');
                } finally {
                    if(falla) falla.guardando = false;
                }
            }
        }
    }
</script>
@endsection
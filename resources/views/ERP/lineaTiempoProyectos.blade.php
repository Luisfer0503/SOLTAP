@extends('principal')

@section('contenido')
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-full bg-gray-50" x-data="timelineApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20 shrink-0 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-kanban text-indigo-600 mr-2"></i> Línea de Tiempo de Proyectos
        </h2>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="filtro" placeholder="Buscar proyecto..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm w-64">
            </div>
            <label class="flex items-center space-x-2 cursor-pointer text-sm text-gray-700">
                <input type="checkbox" x-model="mostrarVacias" class="rounded text-indigo-600 focus:ring-indigo-500">
                <span>Mostrar etapas vacías</span>
            </label>
        </div>
    </header>

    <div class="flex-1 overflow-x-auto overflow-y-hidden p-6 bg-gray-200">
        <div class="flex h-full gap-4 items-start w-max">
            <template x-for="col in columnasFiltradas" :key="col.id">
                <div class="w-72 flex flex-col rounded-xl shadow-sm border max-h-full shrink-0 transition-colors"
                     :class="dragOverCol == col.id ? 'border-indigo-500 bg-indigo-50' : 'bg-gray-100 border-gray-300'"
                     @dragover.prevent="dragOverCol = col.id"
                     @dragleave="dragOverCol = null"
                     @drop="drop($event, col.id); dragOverCol = null">
                    <div class="p-3 border-b border-gray-300 bg-white rounded-t-xl sticky top-0 z-10 flex justify-between items-center shadow-sm">
                        <h3 class="font-bold text-gray-800 text-xs uppercase truncate pr-2" :title="col.nombre" x-text="col.nombre"></h3>
                        <span class="bg-indigo-100 text-indigo-800 text-[10px] font-black px-2 py-0.5 rounded-full" x-text="col.proyectos.length"></span>
                    </div>
                    
                    <div class="p-3 overflow-y-auto flex-1 space-y-3 custom-scrollbar">
                        <template x-for="p in col.proyectos" :key="p.proyecto_id">
                            <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-indigo-300 transition cursor-grab active:cursor-grabbing relative overflow-hidden group"
                                 draggable="true"
                                 @dragstart="dragStart($event, p.proyecto_id, col.id)">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                                <h4 class="font-bold text-gray-900 text-sm mb-1 truncate" :title="p.nombre_proyecto" x-text="p.nombre_proyecto"></h4>
                                <p class="text-xs text-gray-500 mb-2 truncate" :title="p.cliente_nombre"><i class="ph ph-user mr-1"></i><span x-text="p.cliente_nombre || 'Sin cliente'"></span></p>
                                
                                <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-500 font-mono">
                                    <div class="flex items-center">
                                        <i class="ph ph-clock mr-1 text-indigo-400"></i>
                                        <span x-text="p.fecha_formateada"></span>
                                    </div>
                                    <a :href="`{{ url('erp/detalle-proyecto') }}/${p.proyecto_id}`" class="text-indigo-600 hover:text-indigo-800 font-bold" target="_blank" title="Ver Detalles del Proyecto">
                                        <i class="ph ph-arrow-square-out text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </template>
                        <div x-show="col.proyectos.length === 0" class="text-center p-4 text-gray-400 text-xs italic">
                            Sin proyectos
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>

<script>
    function timelineApp() {
        return {
            filtro: '',
            mostrarVacias: false,
            interacciones: @json($interacciones),
            proyectosPorInteraccion: @json($proyectosPorInteraccion),
            dragOverCol: null,
            
            get columnasFiltradas() {
                const search = this.filtro.toLowerCase();
                return this.interacciones.map(int => {
                    const idInt = int.id || int.interaccion_id;
                    let proys = this.proyectosPorInteraccion[idInt] || [];
                    if (search) proys = proys.filter(p => (p.nombre_proyecto && p.nombre_proyecto.toLowerCase().includes(search)) || (p.cliente_nombre && p.cliente_nombre.toLowerCase().includes(search)));
                    proys = proys.map(p => {
                        // Si no está formateada o fue actualizada recientemente por arrastre
                        if (!p.fecha_formateada || p.actualizado) {
                            p.fecha_formateada = new Date(p.fecha_interaccion).toLocaleString('es-MX', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
                            p.actualizado = false;
                        }
                        return p;
                    });
                    return { id: idInt, nombre: int.nombre, proyectos: proys };
                }).filter(col => this.mostrarVacias ? true : col.proyectos.length > 0);
            },

            dragStart(event, proyectoId, sourceColId) {
                event.dataTransfer.setData('proyecto_id', proyectoId);
                event.dataTransfer.setData('source_col_id', sourceColId);
            },

            async drop(event, targetColId) {
                const proyectoId = event.dataTransfer.getData('proyecto_id');
                const sourceColId = event.dataTransfer.getData('source_col_id');

                // Retornar si no es un elemento válido o si se suelta en la misma columna
                if (!proyectoId || !sourceColId || sourceColId == targetColId) return;

                const sourceArray = this.proyectosPorInteraccion[sourceColId] || [];
                const pIndex = sourceArray.findIndex(p => p.proyecto_id == proyectoId);
                
                if (pIndex > -1) {
                    const proyecto = sourceArray[pIndex];
                    
                    // 1. Obtener el nombre de la columna (etapa) destino
                    const targetCol = this.interacciones.find(i => (i.id || i.interaccion_id) == targetColId);
                    const targetName = targetCol ? targetCol.nombre : 'esta etapa';

                    // 2. Preguntar confirmación y permitir un comentario opcional
                    const comentarioUser = prompt(`¿Desea cambiar el estatus del proyecto "${proyecto.nombre_proyecto}" a "${targetName}"?\n\nSi está de acuerdo, puede escribir un comentario opcional para el historial:`, "ACTUALIZADO MEDIANTE ARRASTRE EN LÍNEA DE TIEMPO");
                    if (comentarioUser === null) {
                        return; // Si el usuario cancela, detenemos el proceso
                    }

                    // Actualizar fecha localmente en tiempo real
                    proyecto.fecha_interaccion = new Date().toISOString();
                    proyecto.actualizado = true;

                    // Remover de columna origen
                    sourceArray.splice(pIndex, 1);
                    
                    // Insertar en columna destino
                    if (!this.proyectosPorInteraccion[targetColId]) {
                        this.proyectosPorInteraccion[targetColId] = [];
                    }
                    this.proyectosPorInteraccion[targetColId].unshift(proyecto);

                    // Forzar reactividad para que Alpine re-renderice
                    this.proyectosPorInteraccion = { ...this.proyectosPorInteraccion };

                    // Guardar interacción en el backend utilizando la ruta que ya tienes disponible
                    try {
                        const response = await fetch('{{ route("guardarInteraccionProduccion") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                proyecto_id: proyectoId,
                                interaccion_id: targetColId,
                                comentarios: comentarioUser.trim() || 'ACTUALIZADO MEDIANTE ARRASTRE EN LÍNEA DE TIEMPO'
                            })
                        });
                        
                        const data = await response.json();
                        if (!data.success) {
                            alert('Error al guardar el estatus en el servidor: ' + (data.message || 'Desconocido'));
                            window.location.reload(); // Recarga y revierte el tablero si falla
                        }
                    } catch(e) {
                        console.error(e);
                        alert('Error de conexión.');
                        window.location.reload();
                    }
                }
            }
        }
    }
</script>
@endsection
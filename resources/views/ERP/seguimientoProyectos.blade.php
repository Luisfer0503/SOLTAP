@extends('principal')

@section('contenido')

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">

    <header class="bg-white border-b px-8 py-5 shadow-sm z-10">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="ph ph-kanban text-blue-600 mr-3"></i> Seguimiento de Proyectos
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestiona tu tubería de ventas y prioridades.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('altaProspectos') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm text-sm font-medium flex items-center">
                    <i class="ph ph-user-plus mr-2"></i> Nuevo Prospecto
                </a>
            </div>
        </div>
    </header>

    <div class="px-8 pt-6 pb-2">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                    <i class="ph ph-folder-open text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Activos</p>
                    <p class="text-xl font-bold text-gray-800">{{ count($proyectos) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-8 pb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Proyecto / Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Progreso</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                @foreach($proyectos as $p)
                <tbody class="bg-white border-b border-gray-200" x-data="{ 
                    expanded: false, 
                    articles: {{ json_encode($p->articulos) }}.map(a => ({
                        ...a, 
                        notas: '', 
                        fallas: '', 
                        checks: [false, false, false] 
                    })),
                    projectId: {{ $p->id }},
                    progressP: 0,
                    progressDV: 0,
                    progressL: 0,
                    labels: ['P', 'DV', 'L'],
                    init() {
                        this.calculateProgress();
                    },
                    toggleRow() {
                        this.expanded = !this.expanded;
                    },
                    toggleCheck(artIndex, checkIndex) {
                        this.articles[artIndex].checks[checkIndex] = !this.articles[artIndex].checks[checkIndex];
                        this.calculateProgress();
                    },
                    calculateProgress() {
                        if (this.articles.length === 0) { this.progressP = 0; this.progressDV = 0; this.progressL = 0; return; }
                        let total = this.articles.length;
                        
                        this.progressP = Math.round((this.articles.reduce((acc, art) => acc + (art.checks[0] ? 1 : 0), 0) / total) * 100);
                        this.progressDV = Math.round((this.articles.reduce((acc, art) => acc + (art.checks[1] ? 1 : 0), 0) / total) * 100);
                        this.progressL = Math.round((this.articles.reduce((acc, art) => acc + (art.checks[2] ? 1 : 0), 0) / total) * 100);
                    },
                    getRowProgress(art) {
                        let checked = art.checks.filter(Boolean).length;
                        return Math.round((checked / 3) * 100);
                    }
                }">
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100 cursor-pointer" @click="toggleRow()">
                            
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-3 text-gray-400">
                                    <i class="ph" :class="expanded ? 'ph-caret-down' : 'ph-caret-right'"></i>
                                </div>
                                <div class="flex-shrink-0 h-10 w-10 rounded bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                    {{ substr($p->nombre, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $p->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->cliente ?? 'Sin Cliente' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-middle">
                            <div class="flex flex-col space-y-1 w-40">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-blue-600 w-6">P</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 mx-2">
                                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" :style="`width: ${progressP}%`"></div>
                                    </div>
                                    <span class="w-8 text-right font-bold text-gray-600" x-text="progressP + '%'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-purple-600 w-6">DV</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 mx-2">
                                        <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500" :style="`width: ${progressDV}%`"></div>
                                    </div>
                                    <span class="w-8 text-right font-bold text-gray-600" x-text="progressDV + '%'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-orange-600 w-6">L</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 mx-2">
                                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-500" :style="`width: ${progressL}%`"></div>
                                    </div>
                                    <span class="w-8 text-right font-bold text-gray-600" x-text="progressL + '%'"></span>
                                </div>
                            </div>
                            <!-- Desglose de porcentaje por artículo en la fila principal -->
                            <div class="mt-2 flex flex-wrap gap-1 w-64">
                                <template x-for="art in articles" :key="art.id">
                                    <div class="text-[10px] px-1.5 py-0.5 rounded border border-gray-200 flex items-center" 
                                         :class="getRowProgress(art) == 100 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-600'">
                                        <span class="font-bold mr-1" x-text="(art.articulo_produccion_id || art.nombre.substring(0,3)) + ':'"></span>
                                        <span x-text="getRowProgress(art) + '%'"></span>
                                    </div>
                                </template>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($p->estatus == 'Cotización Pendiente')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 border border-orange-200">
                                    Por Cotizar
                                </span>
                            @elseif($p->estatus == 'En Proceso')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    En Diseño
                                </span>
                            @elseif($p->estatus == 'Cerrado / Ganado')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="ph ph-check mr-1"></i> Ganado
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $p->estatus }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                
                                <a href="{{ route('detalleProyecto', $p->id) }}" class="text-blue-500 hover:text-blue-700 p-1 rounded bg-white inline-flex items-center justify-center" title="Ver Detalles">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Fila de desglose de artículos -->
                    <tr x-show="expanded" x-collapse class="bg-gray-50 border-b border-gray-200">
                        <td colspan="4" class="p-4">
                            <div class="pl-14">
                                <div class="mb-2 font-bold text-gray-700 text-sm flex items-center">
                                    <i class="ph ph-package mr-2"></i> Desglose de Artículos
                                </div>

                                <div x-show="articles.length === 0" class="text-sm text-gray-500 py-2 italic">
                                    No hay artículos registrados para este proyecto.
                                </div>

                                <div x-show="articles.length > 0" class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-100 text-xs text-gray-500 uppercase">
                                            <tr>
                                                <th class="px-4 py-3 text-left w-1/3">Artículo</th>
                                                <th class="px-4 py-3 text-center w-1/4">Verificación (Etapas)</th>
                                                <th class="px-4 py-3 text-left">Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="(art, index) in articles" :key="art.id">
                                                <tr>
                                                    <td class="px-4 py-3 align-top">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 border border-gray-200 overflow-hidden flex items-center justify-center">
                                                                <template x-if="art.imagen">
                                                                    <img :src="art.imagen" class="w-full h-full object-cover">
                                                                </template>
                                                                <template x-if="!art.imagen">
                                                                    <i class="ph ph-image text-xl text-gray-400"></i>
                                                                </template>
                                                            </div>
                                                            <div>
                                                                <div class="font-bold text-gray-800" x-text="(art.articulo_produccion_id ? art.articulo_produccion_id + ' - ' : '') + art.nombre"></div>
                                                                <div class="text-xs text-gray-500 mb-1" x-text="art.descripcion"></div>
                                                                <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-bold">Cant: <span x-text="art.cantidad"></span></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 align-middle">
                                                        <div class="flex flex-col items-center">
                                                            <div class="mb-2">
                                                                <span class="text-xs font-bold" :class="getRowProgress(art) === 100 ? 'text-green-600' : 'text-gray-600'" x-text="getRowProgress(art) + '%'"></span>
                                                            </div>
                                                            <div class="flex justify-center space-x-2">
                                                            <template x-for="(check, i) in art.checks">
                                                                <div class="flex flex-col items-center cursor-pointer group" @click="toggleCheck(index, i)">
                                                                    <div class="w-8 h-8 rounded-lg border-2 flex items-center justify-center transition-all duration-200 shadow-sm mb-1"
                                                                         :class="check ? (i===0?'bg-blue-500 border-blue-500':(i===1?'bg-purple-500 border-purple-500':'bg-orange-500 border-orange-500')) + ' text-white' : 'bg-white border-gray-300 text-gray-300 hover:border-gray-400'">
                                                                        <i class="ph ph-check text-lg" x-show="check"></i>
                                                                        <i class="ph ph-circle text-lg" x-show="!check"></i>
                                                                    </div>
                                                                    <span class="text-[10px] font-bold" :class="i===0?'text-blue-600':(i===1?'text-purple-600':'text-orange-600')" x-text="labels[i]"></span>
                                                                </div>
                                                            </template>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        <div class="space-y-2">
                                                            <div class="relative">
                                                                <i class="ph ph-note-pencil absolute top-2 left-2 text-gray-400 text-xs"></i>
                                                                <input type="text" x-model="art.notas" placeholder="Agregar nota general..." class="w-full pl-7 text-xs py-1.5 rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 focus:bg-white transition">
                                                            </div>
                                                            <div class="relative">
                                                                <i class="ph ph-warning absolute top-2 left-2 text-red-400 text-xs"></i>
                                                                <input type="text" x-model="art.fallas" placeholder="Reportar falla o incidencia..." class="w-full pl-7 text-xs py-1.5 rounded border-red-200 bg-red-50 focus:ring-red-500 focus:border-red-500 text-red-800 placeholder-red-300 focus:bg-white transition">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
                @endforeach
            </table>
        </div>
    </div>

</main>

@endsection
@extends('principal')

@section('contenido')

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="seguimientoApp()">

    <header class="bg-white border-b px-8 py-5 shadow-sm z-10">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="ph ph-kanban text-blue-600 mr-3"></i> Seguimiento de Proyectos
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestiona tu tubería de ventas y prioridades.</p>
            </div>
            <div class="flex space-x-3">
                @if(request()->has('proyecto_id'))
                <a href="{{ route('seguimientoProyectos') }}" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-200 shadow-sm text-sm font-medium flex items-center">
                    <i class="ph ph-list mr-2"></i> Ver Todos
                </a>
                @endif
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
                    expanded: {{ request()->has('proyecto_id') ? 'true' : 'false' }}, 
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
                                                        <div class="space-y-2 w-64">
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" @click="verFallasArticulo(art)" class="shrink-0 px-2 py-1.5 bg-gray-600 text-white rounded text-xs font-bold hover:bg-gray-700 flex items-center shadow-sm" title="Ver Historial de Fallas">
                                                                    <i class="ph ph-list-magnifying-glass mr-1"></i> Ver Fallas
                                                                </button>
                                                                <button type="button" @click="abrirModalFalla({ id: {{ $p->id }}, nombre: '{{ addslashes($p->nombre) }}', disenador: '{{ addslashes($p->disenador ?? '') }}' }, art)" class="shrink-0 px-2 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 flex items-center shadow-sm">
                                                                    <i class="ph ph-plus mr-1"></i> Falla
                                                                </button>
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

    <!-- Modal Falla -->
    <div x-show="modalFallaOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col" @click.stop>
            <div class="p-4 border-b bg-red-50 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold text-red-800"><i class="ph ph-warning mr-2"></i> Reporte de Falla</h3>
                <button @click="modalFallaOpen = false" class="text-red-500 hover:text-red-800"><i class="ph ph-x text-xl"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">FECHA (Automático)</label>
                        <input type="text" x-model="formFalla.fecha" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">SEM (Automático)</label>
                        <input type="text" x-model="formFalla.sem" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">MES (Automático)</label>
                        <input type="text" x-model="formFalla.mes" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">PROYECTO (Automático)</label>
                        <input type="text" x-model="formFalla.proyecto_nombre" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">DISEÑADOR (Automático)</label>
                        <input type="text" x-model="formFalla.disenador" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">MUEBLE (Automático)</label>
                        <input type="text" x-model="formFalla.mueble" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 text-gray-600 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">CANTIDAD AFECTADA <span class="text-red-500">*</span></label>
                        <input type="number" x-model="formFalla.cantidad" min="1" class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white font-bold text-gray-800">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">CATEGORÍA DE FALLA <span class="text-red-500">*</span></label>
                        <select x-model="formFalla.falla_categoria" class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-gray-800">
                            <option value="">Seleccione Categoría</option>
                            <template x-for="cat in categoriasFallas" :key="cat.id">
                                <option :value="cat.id" x-text="cat.nombre"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">SUB CATEGORÍA DE FALLA</label>
                        <select x-model="formFalla.falla_subcategoria" class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-gray-800">
                            <option value="">Seleccione Subcategoría</option>
                            <template x-for="sub in subcategoriasFiltradas" :key="sub.id">
                                <option :value="sub.id" x-text="sub.nombre"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">ORIGINÓ <span class="text-red-500">*</span></label>
                        <select x-model="formFalla.origino" multiple class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white h-28 text-gray-800">
                            <template x-for="u in usuarios" :key="u.id">
                                <option :value="u.id" x-text="u.name"></option>
                            </template>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1"><i class="ph ph-mouse-left"></i> Ctrl+Click para selección múltiple</p>
                    </div>
                    <div class="flex flex-col justify-end pb-5">
                        <label class="block text-xs font-bold text-gray-500 mb-1">ÁREA DE ORIGEN (Automático)</label>
                        <textarea :value="areasOrigino" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 resize-none text-gray-600 font-medium" rows="2" placeholder="Se llena automático al seleccionar usuario"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">RESOLVIÓ <span class="text-red-500">*</span></label>
                        <select x-model="formFalla.resolvio" multiple class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white h-28 text-gray-800">
                            <template x-for="u in usuarios" :key="u.id">
                                <option :value="u.id" x-text="u.name"></option>
                            </template>
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1"><i class="ph ph-mouse-left"></i> Ctrl+Click para selección múltiple</p>
                    </div>
                    <div class="flex flex-col justify-end pb-5">
                        <label class="block text-xs font-bold text-gray-500 mb-1">ÁREA SOLUCIÓN (Automático)</label>
                        <textarea :value="areasResolvio" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded focus:ring-0 resize-none text-gray-600 font-medium" rows="2" placeholder="Se llena automático al seleccionar usuario"></textarea>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1">DESCRIPCIÓN <span class="text-red-500">*</span></label>
                    <textarea x-model="formFalla.descripcion" rows="3" class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-gray-800" placeholder="Detalle la falla reportada..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">HH MINUTOS <span class="text-red-500">*</span></label>
                        <input type="number" x-model="formFalla.hh_minutos" min="0" placeholder="Ej. 120 (Equivale a 2 horas)" class="w-full text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-gray-800 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">HH COSTOS APROX (Automático $46.07/hr)</label>
                        <input type="text" :value="'$ ' + costoHhAprox.toFixed(2)" readonly class="w-full text-sm border-gray-300 bg-gray-100 rounded font-bold text-red-700 focus:ring-0">
                    </div>
                </div>

                <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-xs font-bold text-gray-700">MATERIALES ASOCIADOS</label>
                        <button type="button" @click="formFalla.materiales.push({material: '', costo: ''})" class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded border border-blue-200 font-bold hover:bg-blue-100"><i class="ph ph-plus"></i> Agregar Fila Material</button>
                    </div>
                    <template x-for="(mat, idx) in formFalla.materiales" :key="idx">
                        <div class="flex gap-2 mb-2 items-center">
                            <input type="text" x-model="mat.material" placeholder="Descripción del material..." class="flex-1 text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-gray-800">
                            <span class="text-gray-500 font-bold">$</span>
                            <input type="number" x-model="mat.costo" placeholder="Costo" class="w-32 text-sm border-gray-300 focus:border-red-500 focus:ring-red-500 rounded bg-white text-right font-bold text-gray-800">
                            <button type="button" @click="formFalla.materiales.splice(idx, 1)" class="text-red-500 hover:text-red-700 text-lg"><i class="ph ph-trash"></i></button>
                        </div>
                    </template>
                    <div class="text-right mt-3 border-t pt-3">
                        <span class="text-xs font-bold text-gray-500">COSTO MATERIALES (Automático):</span>
                        <span class="text-base font-bold text-red-700 ml-2" x-text="'$ ' + costoMateriales.toFixed(2)"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 items-end mt-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">REPORTE (Evidencia Fotográfica)</label>
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="cursor-pointer bg-red-50 hover:bg-red-100 text-red-700 font-semibold py-1.5 px-4 rounded-full text-xs transition border border-red-200">
                                <i class="ph ph-upload-simple mr-1"></i> Subir Archivo
                                <input type="file" @change="handleReporteFile" accept="image/*" class="hidden">
                            </label>
                            <label class="cursor-pointer bg-red-600 hover:bg-red-700 text-white font-semibold py-1.5 px-4 rounded-full text-xs transition shadow-sm">
                                <i class="ph ph-camera mr-1"></i> Tomar Foto
                                <input type="file" @change="handleReporteFile" accept="image/*" capture="environment" class="hidden">
                            </label>
                        </div>
                        <template x-if="formFalla.reportePreview">
                            <img :src="formFalla.reportePreview" class="mt-3 h-24 rounded border border-gray-300 object-contain shadow-sm p-1 bg-white">
                        </template>
                    </div>
                    <div class="text-right p-4 bg-red-100 rounded-lg border border-red-200 shadow-inner">
                        <span class="text-xs font-bold text-red-800 uppercase block mb-1">COSTO TOTAL DE LA FALLA (Automático):</span>
                        <span class="text-3xl font-black text-red-700 block" x-text="'$ ' + costoTotal.toFixed(2)"></span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-3 shrink-0">
                <button @click="modalFallaOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition">Cancelar</button>
                <button @click="guardarFallaEnBd()" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition flex items-center shadow-md" :disabled="guardandoFalla">
                    <i class="ph ph-floppy-disk mr-2" x-show="!guardandoFalla"></i>
                    <i class="ph ph-spinner animate-spin mr-2" x-show="guardandoFalla"></i>
                    <span x-text="guardandoFalla ? 'Guardando...' : 'Guardar Falla'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Ver Historial de Fallas -->
    <div x-show="modalVerFallasOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col" @click.stop>
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold text-gray-800"><i class="ph ph-list-magnifying-glass mr-2"></i> Fallas Registradas en Artículo</h3>
                <button @click="modalVerFallasOpen = false" class="text-gray-500 hover:text-gray-800"><i class="ph ph-x text-xl"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-gray-100">
                <div x-show="cargandoFallas" class="text-center py-8">
                    <i class="ph ph-spinner animate-spin text-3xl text-gray-400"></i>
                    <p class="text-gray-500 mt-2 text-sm font-bold">Cargando registro de fallas...</p>
                </div>
                
                <div x-show="!cargandoFallas && fallasList.length === 0" class="text-center py-8 text-gray-500 italic bg-white rounded-lg border border-dashed border-gray-300">
                    No se han reportado fallas para este artículo.
                </div>
                
                <div x-show="!cargandoFallas && fallasList.length > 0" class="space-y-4">
                    <template x-for="falla in fallasList" :key="falla.id">
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm relative">
                            <div class="flex justify-between items-start mb-3 border-b border-gray-100 pb-3">
                                <div>
                                    <span class="bg-red-100 text-red-800 text-xs font-black px-2 py-1 rounded-full uppercase" x-text="(falla.categoria_nombre || 'Sin categoría') + (falla.subcategoria_nombre ? ' / ' + falla.subcategoria_nombre : '')"></span>
                                    <span class="text-xs font-bold ml-2 text-gray-500" x-text="'Reportado el: ' + (falla.fecha_hora_formateada || falla.fecha)"></span>
                                </div>
                                <div class="text-right">
                                    <div class="mb-1 text-[10px] text-gray-500 font-medium bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100 inline-block">
                                        <i class="ph ph-user text-gray-400"></i> Registró: <span class="font-bold text-gray-700" x-text="falla.registrado_por_nombre"></span>
                                    </div>
                                    <span class="text-xl font-black text-red-600 block" x-text="'$ ' + parseFloat(falla.costo_total).toFixed(2)"></span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Costo Total Falla</span>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-700 mb-4 bg-gray-50 p-3 rounded" x-text="falla.descripcion"></p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4 text-xs">
                                <div class="bg-red-50 p-2 rounded border border-red-100">
                                    <span class="font-bold text-red-800 uppercase block mb-1">Originó:</span>
                                    <span class="text-gray-800" x-text="falla.origino_nombres && falla.origino_nombres.length > 0 ? falla.origino_nombres.join(', ') : 'No especificado'"></span>
                                </div>
                                <div class="bg-green-50 p-2 rounded border border-green-100">
                                    <span class="font-bold text-green-800 uppercase block mb-1">Resolvió:</span>
                                    <span class="text-gray-800" x-text="falla.resolvio_nombres && falla.resolvio_nombres.length > 0 ? falla.resolvio_nombres.join(', ') : 'No especificado'"></span>
                                </div>
                            </div>

                            <div x-show="falla.materiales_lista && falla.materiales_lista.length > 0" class="mb-4 bg-white border border-gray-200 rounded-lg p-3">
                                <span class="font-bold text-gray-600 uppercase text-[10px] block mb-2">Materiales Asociados:</span>
                                <ul class="list-disc pl-5 text-xs text-gray-700 space-y-1">
                                    <template x-for="(mat, idx) in falla.materiales_lista" :key="idx">
                                        <li><span x-text="mat.material"></span> - <span class="font-bold" x-text="'$ ' + parseFloat(mat.costo || 0).toFixed(2)"></span></li>
                                    </template>
                                </ul>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-gray-600">
                                <div class="bg-gray-50 px-3 py-2 rounded border border-gray-100"><span class="font-bold block text-gray-400 uppercase text-[9px] mb-0.5">Cantidad Afectada</span> <span class="font-bold text-gray-800 text-sm" x-text="falla.cantidad"></span> pzs</div>
                                <div class="bg-gray-50 px-3 py-2 rounded border border-gray-100"><span class="font-bold block text-gray-400 uppercase text-[9px] mb-0.5">Horas Hombre</span> <span class="font-bold text-gray-800 text-sm" x-text="falla.hh_minutos"></span> min</div>
                                <div class="bg-gray-50 px-3 py-2 rounded border border-gray-100"><span class="font-bold block text-gray-400 uppercase text-[9px] mb-0.5">Costo HH Aprox</span> <span class="font-bold text-gray-800 text-sm" x-text="'$' + parseFloat(falla.costo_hh).toFixed(2)"></span></div>
                                <div class="bg-gray-50 px-3 py-2 rounded border border-gray-100"><span class="font-bold block text-gray-400 uppercase text-[9px] mb-0.5">Costo Materiales</span> <span class="font-bold text-gray-800 text-sm" x-text="'$' + parseFloat(falla.costo_materiales).toFixed(2)"></span></div>
                            </div>
                            
                            <template x-if="falla.reporte_imagen">
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    <a :href="'{{ url('storage') }}/' + falla.reporte_imagen" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition bg-blue-50 px-3 py-1.5 rounded-full">
                                        <i class="ph ph-image mr-1 text-base"></i> Ver Evidencia Fotográfica
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end shrink-0">
                <button @click="modalVerFallasOpen = false" class="px-6 py-2 bg-gray-800 text-white rounded-lg font-bold hover:bg-gray-900 transition shadow-sm">Cerrar Historial</button>
            </div>
        </div>
    </div>
</main>

<script>
    function seguimientoApp() {
        return {
            usuarios: @json($usuarios ?? []),
            categoriasFallas: @json($categoriasFallas ?? []),
            subcategoriasFallas: @json($subcategoriasFallas ?? []),
            costoHora: 46.07,
            modalFallaOpen: false,
            guardandoFalla: false,
            modalVerFallasOpen: false,
            cargandoFallas: false,
            fallasList: [],
            proyectoActual: null,
            articuloActual: null,
            formFalla: {
                proyecto_id: null,
                proyecto_nombre: '',
                disenador: '',
                articulo_id: null,
                mueble: '',
                fecha: '',
                sem: '',
                mes: '',
                cantidad: 1,
                falla_categoria: '',
                falla_subcategoria: '',
                origino: [], 
                resolvio: [],
                descripcion: '',
                hh_minutos: '',
                materiales: [],
                reporte: null,
                reportePreview: null
            },

            abrirModalFalla(proyecto, articulo) {
                const d = new Date();
                // Cálculo simple de semana actual
                const startDate = new Date(d.getFullYear(), 0, 1);
                const days = Math.floor((d - startDate) / (24 * 60 * 60 * 1000));
                const weekNumber = Math.ceil((days + startDate.getDay() + 1) / 7);

                this.proyectoActual = proyecto;
                this.articuloActual = articulo;

                this.formFalla = {
                    proyecto_id: proyecto.id,
                    proyecto_nombre: proyecto.nombre,
                    disenador: proyecto.disenador || 'No asignado',
                    articulo_id: articulo.id,
                    mueble: articulo.nombre,
                    fecha: d.toISOString().split('T')[0],
                    sem: weekNumber.toString(),
                    mes: d.toLocaleString('es-MX', { month: 'long' }).toUpperCase(),
                    cantidad: 1,
                    falla_categoria: '',
                    falla_subcategoria: '',
                    origino: [],
                    resolvio: [],
                    descripcion: '',
                    hh_minutos: '',
                    materiales: [{ material: '', costo: '' }],
                    reporte: null,
                    reportePreview: null
                };
                this.modalFallaOpen = true;
            },

            get areasOrigino() {
                return this.formFalla.origino.map(id => {
                    const u = this.usuarios.find(user => user.id == id);
                    return u ? (u.area_name || 'Sin área registrada') : '';
                }).filter(Boolean).join(', ');
            },

            get areasResolvio() {
                return this.formFalla.resolvio.map(id => {
                    const u = this.usuarios.find(user => user.id == id);
                    return u ? (u.area_name || 'Sin área registrada') : '';
                }).filter(Boolean).join(', ');
            },

            get subcategoriasFiltradas() {
                if (!this.formFalla.falla_categoria) return this.subcategoriasFallas;
                const filtradas = this.subcategoriasFallas.filter(s => 
                    s.categoria_id == this.formFalla.falla_categoria || 
                    s.categoria_falla_id == this.formFalla.falla_categoria
                );
                return filtradas.length > 0 ? filtradas : this.subcategoriasFallas;
            },

            get costoHhAprox() {
                return ((parseFloat(this.formFalla.hh_minutos) || 0) / 60) * this.costoHora;
            },

            get costoMateriales() {
                return this.formFalla.materiales.reduce((sum, item) => sum + (parseFloat(item.costo) || 0), 0);
            },

            get costoTotal() {
                return this.costoHhAprox + this.costoMateriales;
            },

            handleReporteFile(event) {
                const file = event.target.files[0];
                if (file) {
                    this.formFalla.reporte = file;
                    const reader = new FileReader();
                    reader.onload = (e) => this.formFalla.reportePreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            async verFallasArticulo(articulo) {
                this.modalVerFallasOpen = true;
                this.cargandoFallas = true;
                this.fallasList = [];
                
                try {
                    const response = await fetch(`{{ url('/erp/articulos') }}/${articulo.id}/fallas`);
                    if (response.ok) {
                        this.fallasList = await response.json();
                    } else {
                        alert('Ocurrió un error al obtener el historial de fallas.');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión.');
                } finally {
                    this.cargandoFallas = false;
                }
            },

            async guardarFallaEnBd() {
                if (!this.formFalla.cantidad || !this.formFalla.falla_categoria || !this.formFalla.descripcion || this.formFalla.hh_minutos === '') {
                    alert('Por favor complete los campos obligatorios marcados con * (Cantidad, Categoría, Descripción, HH Minutos).');
                    return;
                }

                this.guardandoFalla = true;
                const formData = new FormData();
                formData.append('proyecto_id', this.formFalla.proyecto_id);
                formData.append('articulo_id', this.formFalla.articulo_id);
                formData.append('fecha', this.formFalla.fecha);
                formData.append('sem', this.formFalla.sem);
                formData.append('mes', this.formFalla.mes);
                formData.append('cantidad', this.formFalla.cantidad);
                formData.append('falla_categoria', this.formFalla.falla_categoria);
                formData.append('falla_subcategoria', this.formFalla.falla_subcategoria);
                formData.append('descripcion', this.formFalla.descripcion);
                formData.append('hh_minutos', this.formFalla.hh_minutos);
                formData.append('costo_hh', this.costoHhAprox.toFixed(2));
                formData.append('costo_materiales', this.costoMateriales.toFixed(2));
                formData.append('costo_total', this.costoTotal.toFixed(2));
                formData.append('origino', JSON.stringify(this.formFalla.origino));
                formData.append('resolvio', JSON.stringify(this.formFalla.resolvio));
                
                const matValidos = this.formFalla.materiales.filter(m => m.material.trim() !== '' || m.costo !== '');
                formData.append('materiales', JSON.stringify(matValidos));
                
                if (this.formFalla.reporte) {
                    formData.append('reporte', this.formFalla.reporte);
                }

                try {
                    const response = await fetch('{{ route("guardarFalla") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert('Falla registrada exitosamente y se registró en los movimientos.');
                        this.modalFallaOpen = false;
                    } else {
                        alert('Error: ' + (result.message || 'No se pudo guardar el reporte de falla.'));
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión al intentar guardar la falla.');
                } finally {
                    this.guardandoFalla = false;
                }
            }
        }
    }
</script>

@endsection
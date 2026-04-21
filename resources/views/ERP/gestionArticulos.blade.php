@extends('principal')

@section('contenido')
    <style>
        [x-cloak] { display: none !important; }
        /* Ocultar flechas (spinners) en inputs numéricos */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100" x-data="appArticulos()">
    
    <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0 z-20">
        <div class="flex items-center">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-armchair text-blue-600 mr-2"></i> Alta de Artículos de Producción
            </h2>
            <span class="mx-4 text-gray-300">|</span>
            <div class="flex items-center">
                <span class="text-sm text-gray-500 mr-2">Proyecto:</span>
                <div class="relative" @click.away="showProyectosDropdown = false">
                    <button @click="showProyectosDropdown = !showProyectosDropdown" class="text-sm border-none bg-gray-50 rounded-lg focus:ring-0 font-bold text-blue-700 cursor-pointer flex items-center w-64 justify-between">
                        <span class="truncate" x-text="proyecto_id ? (proyectos.find(p => p.id == proyecto_id)?.nombre || 'Seleccione un proyecto') : 'Seleccione un proyecto'"></span>
                        <i class="ph ph-caret-down ml-2"></i>
                    </button>
                    <div x-show="showProyectosDropdown" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-30">
                        <div class="p-2">
                            <input type="text" x-model="filtroProyecto" @click.stop placeholder="Buscar por nombre o cliente..." class="w-full text-sm border-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            <template x-for="p in proyectosFiltrados" :key="p.id">
                                <a href="#" @click.prevent="const event = { target: { value: p.id } }; confirmarCambioProyecto(event); showProyectosDropdown = false;" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <p class="font-bold truncate" x-text="p.nombre"></p>
                                    <p class="text-xs text-gray-500" x-text="p.cliente_nombre || 'Sin cliente'"></p>
                                </a>
                            </template>
                            <div x-show="proyectosFiltrados.length === 0" class="px-4 py-3 text-center text-xs text-gray-500 italic">
                                No se encontraron proyectos.
                            </div>
                        </div>
                    </div>
                </div>
                <button @click="cargarArticulosProyecto(proyecto_id)" x-show="proyecto_id" class="ml-2 text-blue-600 hover:text-blue-800 text-xs font-bold underline">
                    <i class="ph ph-arrows-clockwise"></i> Recargar
                </button>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <button @click="guardarTodo" class="px-4 py-2 text-white rounded-lg shadow-sm flex items-center text-sm font-medium transition"
                    :class="isDirty ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-800 hover:bg-blue-900'">
                <i class="ph mr-2" :class="isDirty ? 'ph-warning animate-pulse' : 'ph-floppy-disk'"></i>
                <span x-text="isDirty ? 'Guardar Cambios' : 'Guardado'"></span>
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <div class="w-2/5 bg-white border-r border-gray-200 overflow-y-auto shadow-xl z-10 relative">
            
            <!-- Overlay de Advertencia si no hay proyecto -->
            <div x-show="!proyecto_id" class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="bg-orange-100 p-4 rounded-full mb-4">
                    <i class="ph ph-warning text-4xl text-orange-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Seleccione un Proyecto</h3>
                <p class="text-sm text-gray-500">Para comenzar a dar de alta artículos, primero debe seleccionar un proyecto en la barra superior.</p>
            </div>

            <div class="p-6" x-show="proyecto_id">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-xs">1</span>
                    Datos Generales
                </h3>
                
                <form @submit.prevent="agregarArticulo">
                    
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Diferenciador</label>
                            <input type="text" x-model="form.id_articulo_produccion" placeholder="ej. Planta1" class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                            <select x-model="form.categoria_articulo_id" @change="form.nombre = ''" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Categoría --</option>
                                <template x-for="cat in categorias" :key="cat.categoria_articulo_id">
                                    <option :value="cat.categoria_articulo_id" x-text="cat.nombre"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Artículo</label>
                            <select x-model="form.nombre" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Artículo --</option>
                                <template x-for="articulo in articulosFiltrados" :key="articulo.articulo_id">
                                    <option :value="articulo.nombre" x-text="articulo.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Descripción</label>
                        <textarea x-model="form.descripcion" rows="5" class="w-full rounded bg-gray-50 border-gray-300 text-xs" placeholder="Detalles técnicos del articulo..."></textarea>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                        <p class="text-[10px] text-blue-500 font-bold mb-2 uppercase">Dimensiones (cm) y Peso (Kg)</p>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="text-[9px] text-gray-500 block">Alto (cm)</label>
                                <input type="number" step="0.01" x-model="form.alto" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Ancho (cm)</label>
                                <input type="number" step="0.01" x-model="form.ancho" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Profundo (cm)</label>
                                <input type="number" step="0.01" x-model="form.profundo" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Peso (kg)</label>
                                <input type="number" step="0.10" x-model="form.peso" placeholder="0.0" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between items-center border-t border-blue-200 pt-1">
                            <span class="text-[10px] text-blue-800">Cubicaje calculado:</span>
                            <span class="text-sm font-bold text-blue-800"><span x-text="form.cubicaje"></span> m³</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-black text-black mb-1">Cantidad Total</label>
                            <input type="number" x-model="form.cantidad" min="1" class="w-full rounded bg-white border-2 border-black text-center text-lg font-black py-2 shadow-sm focus:ring-black focus:border-black">
                        </div>
                        <div class="flex flex-col justify-center">
                            <label class="flex items-center cursor-pointer mb-1">
                                <input type="checkbox" x-model="form.tiene_division" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <span class="ml-2 text-xs font-bold text-gray-700">¿Tiene División?</span>
                            </label>
                            
                            <div x-show="form.tiene_division" x-transition class="mt-1">
                                <label class="text-[9px] text-gray-500">Piezas divididas:</label>
                                <input type="number" x-model="form.piezas_divididas" class="w-full p-1 text-xs border-gray-300 rounded bg-yellow-50">
                            </div>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 mt-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-xs">2</span>
                            Materiales y Acabados
                        </div>
                        <button type="button" @click="abrirModalMateriales()" class="px-3 py-1 bg-blue-500 text-white rounded text-xs font-bold hover:bg-blue-600 transition flex items-center">
                            <i class="ph ph-plus mr-1"></i> Agregar Materiales
                        </button>
                    </h3>
                    
                    <div class="space-y-4 border-2 border-blue-200 p-4 rounded-lg bg-blue-50 mb-4">
                        
                        <!-- Madera -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_madera" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Madera</span>
                            </label>
                            <div x-show="form.usa_madera" class="mt-2 ml-4">
                                <!-- Lista de maderas seleccionadas -->
                                <template x-for="(madera, index) in form.maderas_seleccionadas" :key="madera.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="madera.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarMaderaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles para agregar nueva madera -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempMadera.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesMadera" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarMaderaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Melamina -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_melamina" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Melamina</span>
                            </label>
                            <div x-show="form.usa_melamina" class="mt-2 ml-4">
                                <!-- Lista de melaminas seleccionadas -->
                                <template x-for="(melamina, index) in form.melaminas_seleccionadas" :key="melamina.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="melamina.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarMelaminaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempMelamina.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesMelamina" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarMelaminaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Textil / Tapicería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_textil" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Tela</span>
                            </label>
                            <div x-show="form.usa_textil" class="mt-2 ml-4">
                                <!-- Lista de telas seleccionadas -->
                                <template x-for="(tela, index) in form.telas_seleccionadas" :key="tela.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="tela.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarTelaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles (Builder) -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempTela.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesTela" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarTelaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cubierta Especial -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_cubierta" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Cubierta</span>
                            </label>
                            <div x-show="form.usa_cubierta" class="mt-2 ml-4">
                                <!-- Lista de cubiertas seleccionadas -->
                                <template x-for="(cubierta, index) in form.cubiertas_seleccionadas" :key="cubierta.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="cubierta.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarCubiertaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempCubierta.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesCubierta" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarCubiertaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Herrería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.usa_herreria" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Herrería</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Imagen de Referencia del Artículo</label>
                        <input type="file" @change="fileChosen" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="mt-2 h-24 rounded object-contain border">
                        </template>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Archivo PDF (Planos)</label>
                        <input type="file" @change="handlePdf" accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="form.pdf">
                            <p class="text-xs text-green-600 mt-1 font-bold" x-text="'Archivo cargado: ' + form.pdf.name"></p>
                        </template>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gray-900 text-white rounded-lg font-bold shadow-md hover:bg-black transition flex justify-center items-center">
                        <i class="ph ph-plus mr-2"></i> Agregar Mueble a Lista
                    </button>

                </form>
            </div>
        </div>

        <div class="w-3/5 bg-gray-100 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-700">Listado de Artículos (<span x-text="articulos.length"></span>)</h3>
                <button x-show="haySeleccionados" @click="abrirModalDuplicarBloque()" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-bold shadow-sm hover:bg-purple-700 transition flex items-center text-sm" x-cloak>
                    <i class="ph ph-copy mr-2"></i> Duplicar Bloque
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">
                                <input type="checkbox" :checked="todosSeleccionados" @change="toggleSeleccionarTodo()" class="rounded text-purple-600 focus:ring-purple-500 cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Articulo</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dimensiones</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Materiales</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in articulos" :key="index">
                            <tr class="hover:bg-gray-50" :class="{'bg-purple-50': item.seleccionado}">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" x-model="item.seleccionado" class="rounded text-purple-600 focus:ring-purple-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded overflow-hidden mr-3">
                                            <template x-if="item.imagen">
                                                <img :src="item.imagen" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!item.imagen">
                                                <i class="ph ph-image text-gray-400 text-xl m-2"></i>
                                            </template>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900" x-text="item.nombre"></div>
                                            <div class="text-xs text-gray-500" x-text="item.id_articulo_produccion"></div>
                                            <template x-if="item.tiene_division">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800">
                                                    Div: <span x-text="item.piezas_divididas"></span> pzs
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-900">
                                        <span x-text="item.alto"></span> x <span x-text="item.ancho"></span> x <span x-text="item.profundo"></span> cm
                                    </div>
                                    <div class="text-[10px] text-gray-500">Vol: <span x-text="item.cubicaje"></span> m³</div>
                                    <div class="text-[10px] text-gray-500">Peso: <span x-text="item.peso"></span> kg</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-if="item.usa_madera">
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-brown-100 text-amber-800 border border-amber-200">Madera</span>
                                        </template>
                                        <template x-if="item.usa_textil">
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-purple-100 text-purple-800 border border-purple-200">Tela</span>
                                        </template>
                                        <template x-if="item.usa_herreria">
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-800 border border-gray-200">Metal</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1 truncate w-32" x-text="item.descripcion"></div>
                                    <template x-if="item.pdf">
                                        <div class="text-[10px] text-red-500 mt-1 flex items-center">
                                            <i class="ph ph-file-pdf mr-1"></i> PDF Adjunto
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="duplicarArticulo(index)" class="text-purple-600 hover:text-purple-900 transition" title="Duplicar">
                                            <i class="ph ph-copy"></i>
                                        </button>
                                        <button @click="editarArticulo(index)" class="text-blue-600 hover:text-blue-900 transition" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        <button @click="eliminarArticulo(index)" class="text-red-600 hover:text-red-900 transition" title="Eliminar">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal para agregar materiales -->
    <div x-show="showModalMateriales" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4" @click.stop>
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <i class="ph ph-tree mr-2"></i> Dar de Alta Material
                </h2>
                <button type="button" @click="cerrarModalMateriales()" class="text-white hover:text-blue-100 text-xl">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Vista de selección de tipo de material -->
                <template x-if="!tipoMaterialSeleccionado">
                    <div>
                        <p class="text-sm text-gray-600 mb-6 text-center">Selecciona el tipo de material que deseas dar de alta:</p>
                        <div class="grid grid-cols-2 gap-4">
                            <button @click="seleccionarTipoMaterial('madera')" class="p-6 border-2 border-gray-300 rounded-lg hover:border-amber-600 hover:bg-amber-50 transition flex flex-col items-center justify-center">
                                <i class="ph ph-tree text-4xl text-amber-600 mb-3"></i>
                                <span class="font-bold text-gray-800">Madera</span>
                            </button>
                            <button @click="seleccionarTipoMaterial('melamina')" class="p-6 border-2 border-gray-300 rounded-lg hover:border-blue-600 hover:bg-blue-50 transition flex flex-col items-center justify-center">
                                <i class="ph ph-squares-four text-4xl text-blue-600 mb-3"></i>
                                <i class="ph ph-stack text-4xl text-blue-600 mb-3"></i>
                                <span class="font-bold text-gray-800">Melamina</span>
                            </button>
                            <button @click="seleccionarTipoMaterial('tela')" class="p-6 border-2 border-gray-300 rounded-lg hover:border-purple-600 hover:bg-purple-50 transition flex flex-col items-center justify-center">
                                <i class="ph ph-briefcase text-4xl text-purple-600 mb-3"></i>
                                <i class="ph ph-t-shirt text-4xl text-purple-600 mb-3"></i>
                                <span class="font-bold text-gray-800">Tela</span>
                            </button>
                            <button @click="seleccionarTipoMaterial('cubierta')" class="p-6 border-2 border-gray-300 rounded-lg hover:border-green-600 hover:bg-green-50 transition flex flex-col items-center justify-center">
                                <i class="ph ph-cube text-4xl text-green-600 mb-3"></i>
                                <span class="font-bold text-gray-800">Cubierta</span>
                            </button>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="cerrarModalMateriales()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Formulario Genérico para Materiales -->
                <template x-if="tipoMaterialSeleccionado">
                    <form @submit.prevent="agregarMaterial" class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 capitalize" x-text="'Alta de ' + tipoMaterialSeleccionado"></h3>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Material</label>
                            <input type="text" x-model="formularioMaterial.nombre" placeholder="Ej. Roble Americano, Melamina Vesto, Lino Gris" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Campos para Madera -->
                            <template x-if="tipoMaterialSeleccionado === 'madera'">
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Chapa</label>
                                        <div class="flex items-center gap-2">
                                            <select x-model="formularioMaterial.chapa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow" required>
                                                <option value="">-- Seleccionar --</option>
                                                <template x-for="chapa in db_chapas" :key="chapa.chapa_id">
                                                    <option :value="chapa.chapa_id" x-text="chapa.nombre"></option>
                                                </template>
                                            </select>
                                            <button @click.prevent="formActivoParaAgregar = 'chapa'; nuevoNombreGenerico = ''" type="button" class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 shrink-0" title="Agregar nueva chapa">
                                                <i class="ph ph-plus"></i>
                                            </button>
                                        </div>
                                        <div x-show="formActivoParaAgregar === 'chapa'" x-transition class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                                            <input type="text" x-model="nuevoNombreGenerico" @keydown.enter.prevent="agregarItemGenerico('chapa')" placeholder="Nombre nueva chapa" class="flex-grow px-2 py-1 text-sm border-gray-300 rounded">
                                            <button @click.prevent="agregarItemGenerico('chapa')" type="button" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">OK</button>
                                            <button @click.prevent="formActivoParaAgregar = null" type="button" class="text-gray-500 hover:text-red-600"><i class="ph ph-x"></i></button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Color (Opcional)</label>
                                        <input type="text" x-model="formularioMaterial.color" placeholder="Ej. Nogal, Chocolate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Campos para Melamina -->
                            <template x-if="tipoMaterialSeleccionado === 'melamina'">
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Proveedor</label>
                                        <div class="flex items-center gap-2">
                                            <select x-model="formularioMaterial.proveedor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow" required>
                                                <option value="">-- Seleccionar --</option>
                                                <template x-for="prov in db_proveedores" :key="prov.proveedor_id">
                                                    <option :value="prov.proveedor_id" x-text="prov.nombre"></option>
                                                </template>
                                            </select>
                                            <button @click.prevent="formActivoParaAgregar = 'proveedor_melamina'; nuevoNombreGenerico = ''" type="button" class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 shrink-0" title="Agregar nuevo proveedor">
                                                <i class="ph ph-plus"></i>
                                            </button>
                                        </div>
                                        <div x-show="formActivoParaAgregar === 'proveedor_melamina'" x-transition class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                                            <input type="text" x-model="nuevoNombreGenerico" @keydown.enter.prevent="agregarItemGenerico('proveedor_melamina')" placeholder="Nombre nuevo proveedor" class="flex-grow px-2 py-1 text-sm border-gray-300 rounded">
                                            <button @click.prevent="agregarItemGenerico('proveedor_melamina')" type="button" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">OK</button>
                                            <button @click.prevent="formActivoParaAgregar = null" type="button" class="text-gray-500 hover:text-red-600"><i class="ph ph-x"></i></button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Color (Opcional)</label>
                                        <input type="text" x-model="formularioMaterial.color" placeholder="Ej. Blanco, Gris" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Dibujo / Textura (Opcional)</label>
                                        <input type="text" x-model="formularioMaterial.dibujo" placeholder="Ej. Liso, Veta, Textil" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </template>

                            <!-- Campos para Tela -->
                            <template x-if="tipoMaterialSeleccionado === 'tela'">
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Proveedor</label>
                                        <div class="flex items-center gap-2">
                                            <select x-model="formularioMaterial.proveedor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow" required>
                                                <option value="">-- Seleccionar --</option>
                                                <template x-for="prov in db_proveedores" :key="prov.proveedor_id">
                                                    <option :value="prov.proveedor_id" x-text="prov.nombre"></option>
                                                </template>
                                            </select>
                                            <button @click.prevent="formActivoParaAgregar = 'proveedor_tela'; nuevoNombreGenerico = ''" type="button" class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 shrink-0" title="Agregar nuevo proveedor">
                                                <i class="ph ph-plus"></i>
                                            </button>
                                        </div>
                                        <div x-show="formActivoParaAgregar === 'proveedor_tela'" x-transition class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                                            <input type="text" x-model="nuevoNombreGenerico" @keydown.enter.prevent="agregarItemGenerico('proveedor_tela')" placeholder="Nombre nuevo proveedor" class="flex-grow px-2 py-1 text-sm border-gray-300 rounded">
                                            <button @click.prevent="agregarItemGenerico('proveedor_tela')" type="button" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">OK</button>
                                            <button @click.prevent="formActivoParaAgregar = null" type="button" class="text-gray-500 hover:text-red-600"><i class="ph ph-x"></i></button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Colección</label>
                                        <div class="flex items-center gap-2">
                                            <select x-model="formularioMaterial.submaterial_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow">
                                                <option value="">-- Opcional --</option>
                                                <template x-for="sub in db_submateriales" :key="sub.submaterial_id">
                                                    <option :value="sub.submaterial_id" x-text="sub.nombre"></option>
                                                </template>
                                            </select>
                                            <button @click.prevent="formActivoParaAgregar = 'submaterial_tela'; nuevoNombreGenerico = ''" type="button" class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 shrink-0" title="Agregar nueva colección">
                                                <i class="ph ph-plus"></i>
                                            </button>
                                        </div>
                                        <div x-show="formActivoParaAgregar === 'submaterial_tela'" x-transition class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                                            <input type="text" x-model="nuevoNombreGenerico" @keydown.enter.prevent="agregarItemGenerico('submaterial_tela')" placeholder="Nombre nueva colección" class="flex-grow px-2 py-1 text-sm border-gray-300 rounded">
                                            <button @click.prevent="agregarItemGenerico('submaterial_tela')" type="button" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">OK</button>
                                            <button @click.prevent="formActivoParaAgregar = null" type="button" class="text-gray-500 hover:text-red-600"><i class="ph ph-x"></i></button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Dibujo / Patrón (Opcional)</label>
                                        <input type="text" x-model="formularioMaterial.dibujo" placeholder="Ej. Liso, Rayas, Floral" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Color (Opcional)</label>
                                        <input type="text" x-model="formularioMaterial.color" placeholder="Ej. Gris Oxford, Beige" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </template>

                            <!-- Campos para Cubierta -->
                            <template x-if="tipoMaterialSeleccionado === 'cubierta'">
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Piedra / Colección</label>
                                    <div class="flex items-center gap-2">
                                        <select x-model="formularioMaterial.submaterial_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-grow" required>
                                            <option value="">-- Seleccionar --</option>
                                            <template x-for="sub in db_submateriales" :key="sub.submaterial_id">
                                                <option :value="sub.submaterial_id" x-text="sub.nombre"></option>
                                            </template>
                                        </select>
                                        <button @click.prevent="formActivoParaAgregar = 'submaterial_cubierta'; nuevoNombreGenerico = ''" type="button" class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 shrink-0" title="Agregar nuevo tipo/colección">
                                            <i class="ph ph-plus"></i>
                                        </button>
                                    </div>
                                    <div x-show="formActivoParaAgregar === 'submaterial_cubierta'" x-transition class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                                        <input type="text" x-model="nuevoNombreGenerico" @keydown.enter.prevent="agregarItemGenerico('submaterial_cubierta')" placeholder="Nombre nuevo tipo/colección" class="flex-grow px-2 py-1 text-sm border-gray-300 rounded">
                                        <button @click.prevent="agregarItemGenerico('submaterial_cubierta')" type="button" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">OK</button>
                                        <button @click.prevent="formActivoParaAgregar = null" type="button" class="text-gray-500 hover:text-red-600"><i class="ph ph-x"></i></button>
                                    </div>
                                </div>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Imagen de Referencia</label>
                            <input type="file" @change="handleMaterialImage" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <template x-if="formularioMaterial.imagenPreview">
                                <img :src="formularioMaterial.imagenPreview" class="mt-3 h-24 rounded object-contain border p-1">
                            </template>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="tipoMaterialSeleccionado = null; formularioMaterial.imagenPreview = null" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                                Atrás
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition flex items-center justify-center">
                                <i class="ph ph-check mr-2"></i> Agregar Material
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
    
    <!-- Modal de Edición de Artículo -->
    <div x-show="showModalEdicion" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 my-8" @click.stop>
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 flex items-center justify-between sticky top-0">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <i class="ph ph-pencil-simple mr-2"></i> Editar Artículo
                </h2>
                <button type="button" @click="cerrarModalEdicion()" class="text-white hover:text-blue-100 text-xl">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(100vh-120px)]">
                <form @submit.prevent="guardarEdicion">
                    
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Diferenciador</label>
                            <input type="text" x-model="form.id_articulo_produccion" placeholder="ej. Planta1" class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                            <select x-model="form.categoria_articulo_id" @change="form.nombre = ''" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Categoría --</option>
                                <template x-for="cat in categorias" :key="cat.categoria_articulo_id">
                                    <option :value="cat.categoria_articulo_id" x-text="cat.nombre"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Artículo</label>
                            <select x-model="form.nombre" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Artículo --</option>
                                <template x-for="articulo in articulosFiltrados" :key="articulo.articulo_id">
                                    <option :value="articulo.nombre" x-text="articulo.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Descripción</label>
                        <textarea x-model="form.descripcion" rows="5" class="w-full rounded bg-gray-50 border-gray-300 text-xs" placeholder="Detalles técnicos extensos..."></textarea>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                        <p class="text-[10px] text-blue-500 font-bold mb-2 uppercase">Dimensiones (cm) y Peso (Kg)</p>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="text-[9px] text-gray-500 block">Alto (cm)</label>
                                <input type="number" step="0.01" x-model="form.alto" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Ancho (cm)</label>
                                <input type="number" step="0.01" x-model="form.ancho" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Profundo (cm)</label>
                                <input type="number" step="0.01" x-model="form.profundo" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Peso (kg)</label>
                                <input type="number" step="0.10" x-model="form.peso" placeholder="0.0" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between items-center border-t border-blue-200 pt-1">
                            <span class="text-[10px] text-blue-800">Cubicaje calculado:</span>
                            <span class="text-sm font-bold text-blue-800"><span x-text="form.cubicaje"></span> m³</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-black text-black mb-1">Cantidad Total</label>
                            <input type="number" x-model="form.cantidad" min="1" class="w-full rounded bg-white border-2 border-black text-center text-lg font-black py-2 shadow-sm focus:ring-black focus:border-black">
                        </div>
                        <div class="flex flex-col justify-center">
                            <label class="flex items-center cursor-pointer mb-1">
                                <input type="checkbox" x-model="form.tiene_division" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                <span class="ml-2 text-xs font-bold text-gray-700">¿Tiene División?</span>
                            </label>
                            
                            <div x-show="form.tiene_division" x-transition class="mt-1">
                                <label class="text-[9px] text-gray-500">Piezas divididas:</label>
                                <input type="number" x-model="form.piezas_divididas" class="w-full p-1 text-xs border-gray-300 rounded bg-yellow-50">
                            </div>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 mt-6 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-xs">2</span>
                        Materiales y Acabados
                    </h3>
                    
                    <div class="space-y-4 border-2 border-blue-200 p-4 rounded-lg bg-blue-50 mb-4">
                        
                        <!-- Madera -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_madera" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Madera</span>
                            </label>
                            <div x-show="form.usa_madera" class="mt-2 ml-4">
                                <!-- Lista de maderas seleccionadas -->
                                <template x-for="(madera, index) in form.maderas_seleccionadas" :key="madera.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="madera.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarMaderaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempMadera.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesMadera" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarMaderaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Melamina -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_melamina" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Melamina</span>
                            </label>
                            <div x-show="form.usa_melamina" class="mt-2 ml-4">
                                <!-- Lista de melaminas seleccionadas -->
                                <template x-for="(melamina, index) in form.melaminas_seleccionadas" :key="melamina.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="melamina.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarMelaminaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempMelamina.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesMelamina" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarMelaminaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Textil / Tapicería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_textil" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Tela</span>
                            </label>
                            <div x-show="form.usa_textil" class="mt-2 ml-4">
                                <!-- Lista de telas seleccionadas -->
                                <template x-for="(tela, index) in form.telas_seleccionadas" :key="tela.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="tela.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarTelaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempTela.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesTela" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarTelaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cubierta Especial -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_cubierta" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Cubierta</span>
                            </label>
                            <div x-show="form.usa_cubierta" class="mt-2 ml-4">
                                <!-- Lista de cubiertas seleccionadas -->
                                <template x-for="(cubierta, index) in form.cubiertas_seleccionadas" :key="cubierta.id">
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded mb-2 border border-gray-200 text-xs">
                                        <span x-text="cubierta.text" class="font-medium text-gray-700"></span>
                                        <button type="button" @click="eliminarCubiertaLista(index)" class="text-red-500 hover:text-red-700 ml-2">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <!-- Controles -->
                                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                    <div class="flex">
                                        <select x-model="tempCubierta.seleccion" class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded-l border-r-0 focus:ring-blue-500 focus:z-10">
                                            <option value="">-- Seleccionar Combinación --</option>
                                            <template x-for="combo in combinacionesCubierta" :key="combo.id"><option :value="combo.id" x-text="combo.text"></option></template>
                                        </select>
                                        <button type="button" @click="agregarCubiertaLista()" class="px-3 py-1 bg-blue-600 text-white rounded-r text-xs font-bold hover:bg-blue-700 flex items-center justify-center shadow-sm">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Herrería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.usa_herreria" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Requiere Herrería</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Imagen de Referencia del Artículo</label>
                        <input type="file" @change="fileChosen" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="mt-2 h-24 rounded object-contain border">
                        </template>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Archivo PDF (Planos/Guías)</label>
                        <input type="file" @change="handlePdf" accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="form.pdf">
                            <p class="text-xs text-green-600 mt-1 font-bold" x-text="'Archivo cargado: ' + form.pdf.name"></p>
                        </template>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="cerrarModalEdicion()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition flex justify-center items-center">
                            <i class="ph ph-check mr-2"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal Duplicar Bloque -->
    <div x-show="showModalDuplicarBloque" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" @click.stop>
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <i class="ph ph-copy mr-2"></i> Duplicar Bloque de Artículos
                </h2>
                <button type="button" @click="cerrarModalDuplicarBloque()" class="text-white hover:text-purple-100 text-xl">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Ingresa el nuevo diferenciador que se aplicará a los artículos seleccionados:</p>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nuevo Diferenciador</label>
                    <input type="text" x-model="nuevoDiferenciadorBloque" @keydown.enter.prevent="duplicarBloque()" placeholder="ej. Planta2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 uppercase font-bold">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="cerrarModalDuplicarBloque()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">Cancelar</button>
                    <button type="button" @click="duplicarBloque()" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition flex items-center justify-center">
                        <i class="ph ph-check mr-2"></i> Duplicar 
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function appArticulos() {
        return {
            categorias: @json($categorias),
            db_materiales: @json($materiales),
            db_submateriales: @json($submateriales),
            db_chapas: @json($chapas),
            db_proveedores: @json($proveedores),
            articulos_catalogo: @json($articulos),
            proyectos: @json($proyectos),
            filtroProyecto: '',
            showProyectosDropdown: false,
            proyecto_id: '',
            cargando: false,
            form: {
                id_articulo_produccion: '',
                categoria_articulo_id: '',
                nombre: '',
                ancho: '',
                alto: '',
                profundo: '',
                peso: '',
                cubicaje: 0,
                cantidad: 1,
                tiene_division: false,
                piezas_divididas: 0,
                descripcion: '',
                usa_madera: false,
                maderas_seleccionadas: [],
                usa_melamina: false,
                melaminas_seleccionadas: [],
                usa_textil: false,
                telas_seleccionadas: [],
                usa_herreria: false,
                usa_cubierta: false,
                cubiertas_seleccionadas: [],
                tipo_madera: '',
                tipo_melamina: '',
                tipo_tela: '',
                tipo_cubierta: '',
                pdf: null,
                seleccionado: false
            },
            imagePreview: null,
            articulos: [],
            showModalMateriales: false,
            tipoMaterialSeleccionado: null,
            isDirty: false, // Flag para cambios sin guardar
            showModalEdicion: false,
            indexEdicion: null,
            showModalDuplicarBloque: false,
            nuevoDiferenciadorBloque: '',
            formularioMaterial: {
                imagenPreview: null,
                nombre: '',
                imagen: null,
                chapa_id: '',
                color: '',
                proveedor_id: '',
                dibujo: '', // Para tela
                submaterial_id: '',
            },
            formActivoParaAgregar: null, // e.g., 'chapa', 'proveedor_melamina', 'submaterial_cubierta'
            nuevoNombreGenerico: '',
            tempMadera: {
                seleccion: ''
            },
            tempMelamina: {
                seleccion: ''
            },
            tempCubierta: {
                seleccion: ''
            },
            tempTela: {
                    seleccion: ''
            },
            
            init() {
                this.$watch('form.alto', () => this.calcularCubicaje());
                this.$watch('form.ancho', () => this.calcularCubicaje());
                this.$watch('form.profundo', () => this.calcularCubicaje());

                window.addEventListener('beforeunload', (event) => {
                    if (this.isDirty) {
                        event.preventDefault();
                        // El mensaje personalizado es ignorado por la mayoría de navegadores modernos,
                        // pero es necesario para activar el diálogo de confirmación.
                        event.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
                        return 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
                    }
                });
            },

            get proyectosFiltrados() {
                if (!this.filtroProyecto) return this.proyectos;
                const search = this.filtroProyecto.toLowerCase();
                return this.proyectos.filter(p => 
                    (p.nombre && p.nombre.toLowerCase().includes(search)) || 
                    (p.cliente_nombre && p.cliente_nombre.toLowerCase().includes(search))
                );
            },

            get haySeleccionados() {
                return this.articulos.some(a => a.seleccionado);
            },

            get todosSeleccionados() {
                return this.articulos.length > 0 && this.articulos.every(a => a.seleccionado);
            },

            toggleSeleccionarTodo() {
                const estado = !this.todosSeleccionados;
                this.articulos.forEach(a => a.seleccionado = estado);
            },

            abrirModalDuplicarBloque() {
                this.nuevoDiferenciadorBloque = '';
                this.showModalDuplicarBloque = true;
            },

            cerrarModalDuplicarBloque() {
                this.showModalDuplicarBloque = false;
            },

            duplicarBloque() {
                if (!this.nuevoDiferenciadorBloque.trim()) {
                    alert('Por favor ingrese un diferenciador válido.');
                    return;
                }
                const seleccionados = this.articulos.filter(a => a.seleccionado);
                const duplicados = seleccionados.map(item => {
                    const copy = { ...item };
                    copy.maderas_seleccionadas = Array.isArray(item.maderas_seleccionadas) ? [...item.maderas_seleccionadas] : [];
                    copy.melaminas_seleccionadas = Array.isArray(item.melaminas_seleccionadas) ? [...item.melaminas_seleccionadas] : [];
                    copy.telas_seleccionadas = Array.isArray(item.telas_seleccionadas) ? [...item.telas_seleccionadas] : [];
                    copy.cubiertas_seleccionadas = Array.isArray(item.cubiertas_seleccionadas) ? [...item.cubiertas_seleccionadas] : [];
                    delete copy.id; 
                    copy.id_articulo_produccion = this.nuevoDiferenciadorBloque.trim();
                    copy.seleccionado = false; 
                    return copy;
                });
                this.isDirty = true;
                this.articulos.unshift(...duplicados);
                this.articulos.forEach(a => a.seleccionado = false);
                this.cerrarModalDuplicarBloque();
            },

            get combinacionesMadera() {
                if (!this.db_materiales) return [];
                return this.db_materiales
                    .filter(m => m.categoria_id == 1) // 1 = Madera
                    .map(m => {
                        const chapa = this.db_chapas.find(c => c.chapa_id == m.chapa_id)?.nombre;
                        
                        let parts = [m.nombre];
                        if(chapa) parts.push(chapa);
                        if(m.color) parts.push(m.color);
                        
                        return { id: m.material_id, text: parts.join(' - ') };
                    });
            },

            get combinacionesMelamina() {
                if (!this.db_materiales) return [];
                return this.db_materiales
                    .filter(m => m.categoria_id == 2) // 2 = Melamina
                    .map(m => {
                        const prov = this.db_proveedores.find(p => p.proveedor_id == m.proveedor_id)?.nombre;
                        
                        let parts = [m.nombre];
                        if(prov) parts.push(prov);
                        if(m.color) parts.push(m.color);
                        if(m.dibujo) parts.push(m.dibujo);
                        
                        return { id: m.material_id, text: parts.join(' - ') };
                    });
            },

            get combinacionesTela() {
                if (!this.db_materiales) return [];
                return this.db_materiales
                    .filter(m => m.categoria_id == 3) // 3 = Tela
                    .map(m => {
                        const prov = this.db_proveedores.find(p => p.proveedor_id == m.proveedor_id)?.nombre;
                        const sub = this.db_submateriales.find(s => s.submaterial_id == m.submaterial_id)?.nombre;
                        
                        let parts = [m.nombre];
                        if(prov) parts.push(prov);
                        if(sub) parts.push(sub);
                        if(m.dibujo) parts.push(m.dibujo);
                        if(m.color) parts.push(m.color);
                        
                        return { id: m.material_id, text: parts.join(' - ') };
                    });
            },

            get combinacionesCubierta() {
                if (!this.db_materiales) return [];
                return this.db_materiales
                    .filter(m => m.categoria_id == 4) // 4 = Cubierta
                    .map(m => {
                        const sub = this.db_submateriales.find(s => s.submaterial_id == m.submaterial_id)?.nombre;
                        let parts = [m.nombre];
                        if(sub) parts.push(sub);
                        return { id: m.material_id, text: parts.join(' - ') };
                    });
            },

            agregarMaderaLista() {
                if (this.tempMadera.seleccion) {
                    const seleccion = this.combinacionesMadera.find(c => c.id == this.tempMadera.seleccion);
                    if (seleccion && !this.form.maderas_seleccionadas.some(m => m.id === seleccion.id)) {
                        this.form.maderas_seleccionadas.push(seleccion);
                    }
                    this.tempMadera.seleccion = '';
                } else {
                    alert('Por favor selecciona una combinación de madera.');
                }
            },
            eliminarMaderaLista(index) {
                this.form.maderas_seleccionadas.splice(index, 1);
            },

            agregarMelaminaLista() {
                if (this.tempMelamina.seleccion) {
                    const seleccion = this.combinacionesMelamina.find(c => c.id == this.tempMelamina.seleccion);
                    if (seleccion && !this.form.melaminas_seleccionadas.some(m => m.id === seleccion.id)) {
                        this.form.melaminas_seleccionadas.push(seleccion);
                    }
                    this.tempMelamina.seleccion = '';
                    this.isDirty = true;
                } else {
                    alert('Por favor selecciona una combinación de melamina.');
                }
            },
            eliminarMelaminaLista(index) {
                this.form.melaminas_seleccionadas.splice(index, 1);
            },

            agregarCubiertaLista() {
                if (this.tempCubierta.seleccion) {
                    const seleccion = this.combinacionesCubierta.find(c => c.id == this.tempCubierta.seleccion);
                    if (seleccion && !this.form.cubiertas_seleccionadas.some(c => c.id === seleccion.id)) {
                        this.form.cubiertas_seleccionadas.push(seleccion);
                    }
                    this.tempCubierta.seleccion = '';
                    this.isDirty = true;
                } else {
                    alert('Por favor selecciona una combinación de cubierta.');
                }
            },
            eliminarCubiertaLista(index) {
                this.form.cubiertas_seleccionadas.splice(index, 1);
            },

            agregarTelaLista() {
                if (this.tempTela.seleccion) {
                    const seleccion = this.combinacionesTela.find(c => c.id == this.tempTela.seleccion);
                    if (seleccion && !this.form.telas_seleccionadas.some(t => t.id === seleccion.id)) {
                        this.form.telas_seleccionadas.push(seleccion);
                    }
                    this.tempTela.seleccion = '';
                    this.isDirty = true;
                } else {
                    alert('Por favor selecciona una combinación de tela.');
                }
            },
            eliminarTelaLista(index) {
                this.form.telas_seleccionadas.splice(index, 1);
            },
            
            // Cálculo matemático automático
            calcularCubicaje() {
                const h = parseFloat(this.form.alto) || 0;
                const w = parseFloat(this.form.ancho) || 0;
                const d = parseFloat(this.form.profundo) || 0;
                this.form.cubicaje = ((h * w * d) / 1000000).toFixed(3); // cm³ a m³
            },
            
            get articulosFiltrados() {
                if (!this.form.categoria_articulo_id) {
                    return [];
                }
                return this.articulos_catalogo.filter(articulo => articulo.categoria_articulo == this.form.categoria_articulo_id);
            },

            handleMaterialImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.formularioMaterial.imagen = file;
                    const reader = new FileReader();
                    reader.onload = (e) => this.formularioMaterial.imagenPreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            fileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => this.imagePreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },            

            validarCampos() {
                let errores = [];

                if (!this.form.id_articulo_produccion) errores.push('El ID del artículo es obligatorio.');
                if (!this.form.categoria_articulo_id) errores.push('La categoría es obligatoria.');
                if (!this.form.nombre) errores.push('El nombre del artículo es obligatorio.');
                if (!this.form.descripcion) errores.push('La descripción es obligatoria.');
                
                if (!this.form.alto || this.form.alto <= 0) errores.push('El alto es obligatorio.');
                if (!this.form.ancho || this.form.ancho <= 0) errores.push('El ancho es obligatorio.');
                if (!this.form.profundo || this.form.profundo <= 0) errores.push('La profundidad es obligatoria.');
                if (!this.form.peso || this.form.peso <= 0) errores.push('El peso es obligatorio.');
                if (!this.form.cantidad || this.form.cantidad <= 0) errores.push('La cantidad es obligatoria.');

                if (this.form.tiene_division && (!this.form.piezas_divididas || this.form.piezas_divididas <= 0)) {
                    errores.push('Si tiene división, debe especificar el número de piezas.');
                }

                const tieneMaterial = 
                    this.form.maderas_seleccionadas.length > 0 ||
                    this.form.melaminas_seleccionadas.length > 0 ||
                    this.form.telas_seleccionadas.length > 0 ||
                    this.form.cubiertas_seleccionadas.length > 0 ||
                    this.form.usa_herreria;

                if (!tieneMaterial) {
                    errores.push('Debe seleccionar al menos un material (Madera, Melamina, Tela, Cubierta o Herrería).');
                }

                return errores;
            },

            agregarArticulo() {
                const errores = this.validarCampos();
                if (errores.length > 0) {
                    alert('Por favor corrija los siguientes errores:\n\n- ' + errores.join('\n- '));
                    return;
                }

                // Copia manual para preservar objetos File y Arrays
                const nuevoItem = { ...this.form };
                nuevoItem.maderas_seleccionadas = [...this.form.maderas_seleccionadas];
                nuevoItem.melaminas_seleccionadas = [...this.form.melaminas_seleccionadas];
                nuevoItem.telas_seleccionadas = [...this.form.telas_seleccionadas];
                nuevoItem.cubiertas_seleccionadas = [...this.form.cubiertas_seleccionadas];
                nuevoItem.imagen = this.imagePreview; 
                nuevoItem.seleccionado = false;
                
                this.isDirty = true;
                this.articulos.unshift(nuevoItem); 
                
                // Limpiar formulario (manteniendo algunos valores lógicos si se desea)
                this.resetForm();
            },


            eliminarArticulo(index) {
                if(confirm('¿Borrar?')) this.articulos.splice(index, 1);
            },

            resetForm() {
                this.form.categoria_articulo_id = '';
                delete this.form.id; // Limpiar ID
                this.form.nombre = '';
                this.form.id_articulo_produccion = '';
                this.form.ancho = ''; this.form.alto = ''; this.form.profundo = '';
                this.form.peso = ''; this.form.cubicaje = 0;
                this.form.descripcion = '';
                this.form.maderas_seleccionadas = [];
                this.form.melaminas_seleccionadas = [];
                this.form.telas_seleccionadas = [];
                this.form.cubiertas_seleccionadas = [];
                this.form.pdf = null;
                this.form.seleccionado = false;
                this.imagePreview = null;
                // No reseteamos los materiales para agilizar carga de muebles similares
            },

            formatoMoneda(valor) {
                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor);
            },

            abrirModalMateriales() {
                this.showModalMateriales = true;
                this.tipoMaterialSeleccionado = null;
                this.formularioMaterial = {
                    nombre: '',
                    imagen: null, 
                    imagenPreview: null,
                    chapa_id: '', color: '', proveedor_id: '',
                    dibujo: '', submaterial_id: '',
                };
            },            

            cerrarModalMateriales() {
                this.showModalMateriales = false;
                this.tipoMaterialSeleccionado = null;
                this.formularioMaterial.imagenPreview = null;
            },

            seleccionarTipoMaterial(tipo) {
                this.tipoMaterialSeleccionado = tipo;
            },

            async agregarMaterial() {
                const formData = new FormData();
                formData.append('tipo_material', this.tipoMaterialSeleccionado);
                
                for (const key in this.formularioMaterial) {
                    if (key !== 'imagenPreview' && this.formularioMaterial[key] !== null) {
                        formData.append(key, this.formularioMaterial[key]);
                    }
                }

                try {
                    const response = await fetch("{{ route('guardarNuevoMaterial') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.db_materiales.push(data.material);
                        alert('Material "' + data.material.nombre + '" guardado en el catálogo.');
                        this.cerrarModalMateriales();
                    } else {
                        if (data.errors) {
                            let errorMessages = Object.values(data.errors).flat().join('\n');
                            alert('Error de validación:\n- ' + errorMessages);
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo guardar el material.'));
                        }
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión al guardar el material.');
                }
            },

            async agregarItemGenerico(tipoForm) {
                if (!this.nuevoNombreGenerico.trim()) {
                    alert('El nombre no puede estar vacío.');
                    return;
                }

                let tipoItem = tipoForm.split('_')[0];
                let url = '';
                let dataKey = '';
                let listToUpdate = '';
                let formFieldToUpdate = '';
                let idKeyInResponse = '';

                switch (tipoItem) {
                    case 'chapa':
                        url = "{{ route('guardarNuevaChapa') }}";
                        dataKey = 'chapa';
                        listToUpdate = 'db_chapas';
                        formFieldToUpdate = 'chapa_id';
                        idKeyInResponse = 'chapa_id';
                        break;
                    case 'proveedor':
                        url = "{{ route('guardarNuevoProveedor') }}";
                        dataKey = 'proveedor';
                        listToUpdate = 'db_proveedores';
                        formFieldToUpdate = 'proveedor_id';
                        idKeyInResponse = 'proveedor_id';
                        break;
                    case 'submaterial':
                        url = "{{ route('guardarNuevoSubmaterial') }}";
                        dataKey = 'submaterial';
                        listToUpdate = 'db_submateriales';
                        formFieldToUpdate = 'submaterial_id';
                        idKeyInResponse = 'submaterial_id';
                        break;
                    default:
                        alert('Tipo de item no reconocido.');
                        return;
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ nombre: this.nuevoNombreGenerico })
                    });
                    const data = await response.json();
                    if (data.success) {
                        const newItem = data[dataKey];
                        this[listToUpdate].push(newItem);
                        this[listToUpdate].sort((a, b) => a.nombre.localeCompare(b.nombre));
                        this.$nextTick(() => { this.formularioMaterial[formFieldToUpdate] = newItem[idKeyInResponse]; });
                        this.nuevoNombreGenerico = '';
                        this.formActivoParaAgregar = null;
                    } else {
                        let errorMessages = data.message || Object.values(data.errors).flat().join('\n');
                        alert('Error:\n- ' + errorMessages);
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión al guardar el nuevo item.');
                }
            },

            editarArticulo(index) {
                this.indexEdicion = index;
                const item = this.articulos[index];
                
                // Copia manual para preservar objetos File y Arrays al editar
                this.form = { ...item };
                this.form.maderas_seleccionadas = Array.isArray(item.maderas_seleccionadas) ? [...item.maderas_seleccionadas] : [];
                this.form.melaminas_seleccionadas = Array.isArray(item.melaminas_seleccionadas) ? [...item.melaminas_seleccionadas] : [];
                this.form.telas_seleccionadas = Array.isArray(item.telas_seleccionadas) ? [...item.telas_seleccionadas] : [];
                this.form.cubiertas_seleccionadas = Array.isArray(item.cubiertas_seleccionadas) ? [...item.cubiertas_seleccionadas] : [];

                this.imagePreview = this.form.imagen || null;
                this.showModalEdicion = true;
            },

            duplicarArticulo(index) {
                this.indexEdicion = null;
                const item = this.articulos[index];
                this.form = { ...item };
                this.form.maderas_seleccionadas = Array.isArray(item.maderas_seleccionadas) ? [...item.maderas_seleccionadas] : [];
                this.form.melaminas_seleccionadas = Array.isArray(item.melaminas_seleccionadas) ? [...item.melaminas_seleccionadas] : [];
                this.form.telas_seleccionadas = Array.isArray(item.telas_seleccionadas) ? [...item.telas_seleccionadas] : [];
                this.form.cubiertas_seleccionadas = Array.isArray(item.cubiertas_seleccionadas) ? [...item.cubiertas_seleccionadas] : [];

                this.imagePreview = this.form.imagen || null;
                // this.form.id_articulo_produccion = ''; // Comentado para mantener la info correcta al duplicar
                delete this.form.id; // Al duplicar, quitamos el ID para que se cree uno nuevo
                this.showModalEdicion = true;
            },

            cerrarModalEdicion() {
                this.showModalEdicion = false;
                this.indexEdicion = null;
                this.resetForm();
                this.imagePreview = null;
            },

            guardarEdicion() {
                const errores = this.validarCampos();
                if (errores.length > 0) {
                    alert('Por favor corrija los siguientes errores:\n\n- ' + errores.join('\n- '));
                    return;
                }

                this.form.imagen = this.imagePreview;
                
                // Copia manual para guardar
                const itemEditado = { ...this.form };
                itemEditado.maderas_seleccionadas = [...this.form.maderas_seleccionadas];
                itemEditado.melaminas_seleccionadas = [...this.form.melaminas_seleccionadas];
                itemEditado.telas_seleccionadas = [...this.form.telas_seleccionadas];
                itemEditado.cubiertas_seleccionadas = [...this.form.cubiertas_seleccionadas];

                if (this.indexEdicion !== null) {
                    // Modo edición: actualizar artículo existente
                    this.articulos[this.indexEdicion] = itemEditado;
                    this.isDirty = true;
                    alert('Artículo actualizado correctamente');
                } else {
                    // Modo duplicación: agregar como nuevo artículo
                    this.articulos.unshift(itemEditado);
                    this.isDirty = true;
                    alert('Artículo duplicado correctamente');
                }
                
                this.cerrarModalEdicion();
            },

            async confirmarCambioProyecto(event) {
                const nuevoId = event.target.value;
                // Si hay cambios sin guardar, guardarlos automáticamente antes de cambiar
                if (this.isDirty) {
                    if (!confirm('Tienes cambios en artículos sin guardar. Se guardarán automáticamente antes de cambiar de proyecto. ¿Deseas continuar?')) {
                        event.target.value = this.proyecto_id; // Revertir selección
                        return;
                    }
                    // Guardar cambios automáticamente
                    const saved = await this.guardarTodo(true);
                    if (!saved) {
                        event.target.value = this.proyecto_id; // Revertir selección si falló el guardado
                        return;
                    }
                }
                this.proyecto_id = nuevoId;
                if(nuevoId) {
                    this.isDirty = false; // Se resetea al cargar un nuevo proyecto
                    this.cargarArticulosProyecto(nuevoId);
                } else {
                    this.articulos = [];
                    this.isDirty = false;
                }
            },

            async cargarArticulosProyecto(id) {
                if(!id) return;
                this.cargando = true;
                try {
                    const response = await fetch(`{{ url('/erp/articulos-proyecto') }}/${id}`);
                    if (!response.ok) {
                        throw new Error('Error al conectar con el servidor: ' + response.status);
                    }
                    const data = await response.json();
                    
                    // Mapeo de datos para asegurar compatibilidad con el formulario
                    this.articulos = data.map(item => {
                        // Asegurar que los campos numéricos sean números o strings válidos
                        // Asegurar que los arrays de materiales existan
                        item.maderas_seleccionadas = Array.isArray(item.maderas_seleccionadas) ? item.maderas_seleccionadas : [];
                        item.melaminas_seleccionadas = Array.isArray(item.melaminas_seleccionadas) ? item.melaminas_seleccionadas : [];
                        item.telas_seleccionadas = Array.isArray(item.telas_seleccionadas) ? item.telas_seleccionadas : [];
                        item.cubiertas_seleccionadas = Array.isArray(item.cubiertas_seleccionadas) ? item.cubiertas_seleccionadas : [];
                        item.cantidad = item.cantidad || 1;
                        item.seleccionado = false;
                        // La imagen ya viene como URL completa desde el controlador
                        return item;
                    });
                    this.isDirty = false; // Marcar como limpio después de cargar
                } catch (error) {
                    console.error('Error cargando artículos:', error);
                    alert('Error al cargar los artículos del proyecto.');
                } finally {
                    this.cargando = false;
                }
            },

            handlePdf(event) {
                const file = event.target.files[0];
                if (file) {
                    this.form.pdf = file;
                }
            },

            async guardarTodo(skipConfirm = false) {
                if (!this.proyecto_id) {
                    alert('Por favor selecciona un proyecto primero.');
                    return;
                }
                if (this.articulos.length === 0) {
                    alert('No hay artículos para guardar.');
                    return;
                }

                if (!skipConfirm && !confirm('¿Estás seguro de sincronizar ' + this.articulos.length + ' artículos? Esto actualizará la base de datos con la lista actual.')) return;

                const formData = new FormData();
                formData.append('proyecto_id', this.proyecto_id);

                this.articulos.forEach((item, index) => {
                    // Datos básicos
                    if(item.id) formData.append(`articulos[${index}][id]`, item.id); // ID para actualizar
                    formData.append(`articulos[${index}][id_articulo_produccion]`, item.id_articulo_produccion || '');
                    formData.append(`articulos[${index}][categoria_articulo_id]`, item.categoria_articulo_id || '');
                    formData.append(`articulos[${index}][nombre]`, item.nombre || '');
                    formData.append(`articulos[${index}][descripcion]`, item.descripcion || '');
                    formData.append(`articulos[${index}][alto]`, item.alto || 0);
                    formData.append(`articulos[${index}][ancho]`, item.ancho || 0);
                    formData.append(`articulos[${index}][profundo]`, item.profundo || 0);
                    formData.append(`articulos[${index}][peso]`, item.peso || 0);
                    formData.append(`articulos[${index}][cubicaje]`, item.cubicaje || 0);
                    formData.append(`articulos[${index}][cantidad]`, item.cantidad || 1);
                    formData.append(`articulos[${index}][tiene_division]`, item.tiene_division ? 1 : 0);
                    formData.append(`articulos[${index}][piezas_divididas]`, item.piezas_divididas || 0);
                    
                    // Imagen (Base64)
                    if (item.imagen && item.imagen.startsWith('data:')) {
                        formData.append(`articulos[${index}][imagen_base64]`, item.imagen);
                    } else if (item.imagen_ruta) {
                        // Enviar la ruta de la imagen existente si no se cambió
                        formData.append(`articulos[${index}][imagen_ruta]`, item.imagen_ruta);
                    }

                    // PDF (Archivo)
                    if (item.pdf instanceof File) {
                        formData.append(`articulos[${index}][pdf_archivo]`, item.pdf);
                    }

                    // Materiales (Agrupamos todos los arrays en una estructura para el backend)
                    const materiales = [
                        ...item.maderas_seleccionadas.map(m => ({ tipo: 'Madera', material_id: m.id })),
                        ...item.melaminas_seleccionadas.map(m => ({ tipo: 'Melamina', material_id: m.id })),
                        ...item.telas_seleccionadas.map(m => ({ tipo: 'Tela', material_id: m.id })),
                        ...item.cubiertas_seleccionadas.map(m => ({ tipo: 'Cubierta', material_id: m.id }))
                    ];
                    if (item.usa_herreria) materiales.push({ tipo: 'Otros', material_id: null, descripcion: 'Herrería' });

                    materiales.forEach((mat, matIndex) => {
                        formData.append(`articulos[${index}][materiales][${matIndex}][tipo]`, mat.tipo);
                        formData.append(`articulos[${index}][materiales][${matIndex}][material_id]`, mat.material_id);
                        if (mat.descripcion) formData.append(`articulos[${index}][materiales][${matIndex}][descripcion]`, mat.descripcion);
                    });
                });

                try {
                    const response = await fetch("{{ url('/erp/guardar-articulos-produccion') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        if (!skipConfirm) alert('Artículos guardados correctamente.');
                        this.isDirty = false; // Marcar como limpio después de guardar
                        if (!skipConfirm) this.cargarArticulosProyecto(this.proyecto_id); // Recargar para obtener IDs actualizados solo si no es automático
                        return true;
                    } else {
                        alert('Error: ' + (data.message || 'Desconocido'));
                        return false;
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error de conexión al guardar');
                    return false;
                }
            }
        }
    }
</script>

@endsection
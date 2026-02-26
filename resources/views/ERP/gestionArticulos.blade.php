@extends('principal')

@section('contenido')

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
                <select x-model="proyecto_id" class="text-sm border-none bg-gray-50 rounded-lg focus:ring-0 font-bold text-blue-700 cursor-pointer">
                    <option value="">Seleccione un proyecto</option>
                    @foreach($proyectos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <button class="px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 shadow-sm flex items-center text-sm font-medium">
                <i class="ph ph-floppy-disk mr-2"></i> Guardar Todo
            </button>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <div class="w-2/5 bg-white border-r border-gray-200 overflow-y-auto shadow-xl z-10">
            <div class="p-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-xs">1</span>
                    Datos Generales
                </h3>
                
                <form @submit.prevent="agregarArticulo">
                    
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">ID Art. Producción</label>
                            <input type="text" x-model="form.id_articulo_produccion" placeholder="ej. PROD-001" class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                            <select x-model="form.material_id" @change="form.nombre = ''" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Categoría --</option>
                                <template x-for="mat in materiales" :key="mat.material_id">
                                    <option :value="mat.material_id" x-text="mat.nombre"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-2">
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
                        <textarea x-model="form.descripcion" rows="2" class="w-full rounded bg-gray-50 border-gray-300 text-xs" placeholder="Detalles técnicos..."></textarea>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                        <p class="text-[10px] text-blue-500 font-bold mb-2 uppercase">Dimensiones (Metros) y Peso (Kg)</p>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="text-[9px] text-gray-500 block">Alto (m)</label>
                                <input type="number" step="0.01" x-model="form.alto" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Ancho (m)</label>
                                <input type="number" step="0.01" x-model="form.ancho" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Profundo (m)</label>
                                <input type="number" step="0.01" x-model="form.profundo" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
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
                            <label class="block text-xs font-bold text-gray-700 mb-1">Cantidad Total</label>
                            <input type="number" x-model="form.cantidad" min="1" class="w-full rounded bg-white border-gray-300 text-center text-sm font-bold">
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
                            <div x-show="form.usa_madera" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Madera</label>
                                    <select x-model="form.tipo_madera" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Pino">Pino</option>
                                        <option value="Roble">Roble</option>
                                        <option value="Cedro">Cedro</option>
                                        <option value="Caoba">Caoba</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Melamina -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_melamina" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Melamina</span>
                            </label>
                            <div x-show="form.usa_melamina" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo Melamina</label>
                                    <select x-model="form.tipo_melamina" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Melamina Blanca">Melamina Blanca</option>
                                        <option value="Melamina Roble">Melamina Roble</option>
                                        <option value="Formica">Formica</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Textil / Tapicería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_textil" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Tela</span>
                            </label>
                            <div x-show="form.usa_textil" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Tela</label>
                                    <select x-model="form.tipo_tela" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Algodón">Algodón</option>
                                        <option value="Lino">Lino</option>
                                        <option value="Poliéster">Poliéster</option>
                                        <option value="Terciopelo">Terciopelo</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Cubierta Especial -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_cubierta" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Cubierta</span>
                            </label>
                            <div x-show="form.usa_cubierta" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Cubierta</label>
                                    <select x-model="form.tipo_cubierta" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Cristal">Cristal</option>
                                        <option value="Mármol">Mármol</option>
                                        <option value="Granito">Granito</option>
                                        <option value="Cuarzo">Cuarzo</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
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

                    <div class="mb-4 pt-2">
                        <div class="flex flex-col space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.requiere_instalacion" class="rounded text-blue-600 w-3 h-3">
                                <span class="ml-2 text-xs text-gray-600">Requiere Instalación</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.requiere_desemplaye" class="rounded text-blue-600 w-3 h-3">
                                <span class="ml-2 text-xs text-gray-600">Requiere Desemplaye</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Imagen / Boceto</label>
                        <input type="file" @change="fileChosen" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="mt-2 h-24 rounded object-contain border">
                        </template>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Comentarios Generales</label>
                        <textarea x-model="form.comentarios" rows="2" class="w-full rounded bg-gray-50 border-gray-300 text-xs"></textarea>
                    </div>

                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 mt-6 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-2 text-xs">3</span>
                        Validación de Acceso
                    </h3>

                    <div class="bg-red-50 border-2 border-red-200 p-4 rounded-lg mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-3">¿Es Planta Baja?</label>
                            <div class="flex gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="form.es_planta_baja" value="si" class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                    <span class="ml-2 text-sm font-semibold text-green-700">Sí - Es Planta Baja</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="form.es_planta_baja" value="no" class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                    <span class="ml-2 text-sm font-semibold text-red-700">No - Requiere Escaleras/Ascensor</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="form.es_planta_baja === 'no'" x-transition>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Excepciones o Condiciones Especiales de Acceso <span class="text-red-600">*</span></label>
                            <textarea x-model="form.condiciones_acceso" rows="3" :required="form.es_planta_baja === 'no'" placeholder="Describe cualquier limitación de acceso, restricciones de movimiento, o condiciones especiales..." class="w-full px-3 py-2 rounded border-2 border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 text-sm font-semibold text-gray-800"></textarea>
                            <p class="text-xs text-red-600 mt-1">Este campo es obligatorio para documentar accesibilidad</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gray-900 text-white rounded-lg font-bold shadow-md hover:bg-black transition flex justify-center items-center">
                        <i class="ph ph-plus mr-2"></i> Agregar Mueble a Lista
                    </button>

                </form>
            </div>
        </div>

        <div class="w-3/5 bg-gray-100 p-8 overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Listado de Artículos (<span x-text="articulos.length"></span>)</h3>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Articulo</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dimensiones</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Materiales</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in articulos" :key="index">
                            <tr class="hover:bg-gray-50">
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
                                        <span x-text="item.alto"></span> x <span x-text="item.ancho"></span> x <span x-text="item.profundo"></span> m
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
                                <span class="font-bold text-gray-800">Melamina</span>
                            </button>
                            <button @click="seleccionarTipoMaterial('tela')" class="p-6 border-2 border-gray-300 rounded-lg hover:border-purple-600 hover:bg-purple-50 transition flex flex-col items-center justify-center">
                                <i class="ph ph-briefcase text-4xl text-purple-600 mb-3"></i>
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
                    <form @submit.prevent="agregarMaterial">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 capitalize" x-text="'Alta de ' + tipoMaterialSeleccionado"></h3>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Material</label>
                            <input type="text" x-model="formularioMaterial.nombre" :placeholder="'Ej. Roble, Lino Gris, Mármol Carrara'" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Imagen de Referencia</label>
                            <input type="file" @change="handleMaterialImage" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <template x-if="formularioMaterial.imagenPreview">
                                <img :src="formularioMaterial.imagenPreview" class="mt-3 h-24 rounded object-contain border p-1">
                            </template>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="tipoMaterialSeleccionado = null" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
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
                            <label class="block text-sm font-bold text-gray-700 mb-2">ID Art. Producción</label>
                            <input type="text" x-model="form.id_articulo_produccion" placeholder="ej. PROD-001" class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm uppercase font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                            <select x-model="form.material_id" @change="form.nombre = ''" required class="w-full px-3 py-2 rounded bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Seleccionar Categoría --</option>
                                <template x-for="mat in materiales" :key="mat.material_id">
                                    <option :value="mat.material_id" x-text="mat.nombre"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-2">
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
                        <textarea x-model="form.descripcion" rows="2" class="w-full rounded bg-gray-50 border-gray-300 text-xs" placeholder="Detalles técnicos..."></textarea>
                    </div>

                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                        <p class="text-[10px] text-blue-500 font-bold mb-2 uppercase">Dimensiones (Metros) y Peso (Kg)</p>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="text-[9px] text-gray-500 block">Alto (m)</label>
                                <input type="number" step="0.01" x-model="form.alto" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Ancho (m)</label>
                                <input type="number" step="0.01" x-model="form.ancho" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="text-[9px] text-gray-500 block">Profundo (m)</label>
                                <input type="number" step="0.01" x-model="form.profundo" @input="calcularCubicaje" placeholder="0.00" class="w-full p-1 text-center text-xs border-gray-300 rounded">
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
                            <label class="block text-xs font-bold text-gray-700 mb-1">Cantidad Total</label>
                            <input type="number" x-model="form.cantidad" min="1" class="w-full rounded bg-white border-gray-300 text-center text-sm font-bold">
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
                            <div x-show="form.usa_madera" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Madera</label>
                                    <select x-model="form.tipo_madera" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Pino">Pino</option>
                                        <option value="Roble">Roble</option>
                                        <option value="Cedro">Cedro</option>
                                        <option value="Caoba">Caoba</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Melamina -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_melamina" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Melamina</span>
                            </label>
                            <div x-show="form.usa_melamina" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo Melamina</label>
                                    <select x-model="form.tipo_melamina" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Melamina Blanca">Melamina Blanca</option>
                                        <option value="Melamina Roble">Melamina Roble</option>
                                        <option value="Formica">Formica</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Textil / Tapicería -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_textil" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Textil / Tapicería</span>
                            </label>
                            <div x-show="form.usa_textil" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Tela</label>
                                    <select x-model="form.tipo_tela" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Algodón">Algodón</option>
                                        <option value="Lino">Lino</option>
                                        <option value="Poliéster">Poliéster</option>
                                        <option value="Terciopelo">Terciopelo</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Cubierta Especial -->
                        <div class="bg-white p-3 rounded-lg border border-blue-200">
                            <label class="flex items-center cursor-pointer mb-3">
                                <input type="checkbox" x-model="form.usa_cubierta" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Lleva Cubierta Especial</span>
                            </label>
                            <div x-show="form.usa_cubierta" class="flex items-center gap-3 mt-2 ml-8">
                                <div class="flex-grow">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Cubierta</label>
                                    <select x-model="form.tipo_cubierta" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="Cristal">Cristal</option>
                                        <option value="Mármol">Mármol</option>
                                        <option value="Granito">Granito</option>
                                        <option value="Cuarzo">Cuarzo</option>
                                    </select>
                                </div>
                                <div class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center flex-shrink-0">
                                    <i class="ph ph-image text-gray-400 text-2xl"></i>
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

                    <div class="mb-4 pt-2">
                        <div class="flex flex-col space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.requiere_instalacion" class="rounded text-blue-600 w-3 h-3">
                                <span class="ml-2 text-xs text-gray-600">Requiere Instalación</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="form.requiere_desemplaye" class="rounded text-blue-600 w-3 h-3">
                                <span class="ml-2 text-xs text-gray-600">Requiere Desemplaye</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Imagen / Boceto</label>
                        <input type="file" @change="fileChosen" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="mt-2 h-24 rounded object-contain border">
                        </template>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Comentarios Generales</label>
                        <textarea x-model="form.comentarios" rows="2" class="w-full rounded bg-gray-50 border-gray-300 text-xs"></textarea>
                    </div>

                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 mt-6 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-2 text-xs">3</span>
                        Validación de Acceso
                    </h3>

                    <div class="bg-red-50 border-2 border-red-200 p-4 rounded-lg mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-3">¿Es Planta Baja?</label>
                            <div class="flex gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="form.es_planta_baja" value="si" class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                    <span class="ml-2 text-sm font-semibold text-green-700">Sí - Es Planta Baja</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" x-model="form.es_planta_baja" value="no" class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                    <span class="ml-2 text-sm font-semibold text-red-700">No - Requiere Escaleras/Ascensor</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="form.es_planta_baja === 'no'" x-transition>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Excepciones o Condiciones Especiales de Acceso <span class="text-red-600">*</span></label>
                            <textarea x-model="form.condiciones_acceso" rows="3" :required="form.es_planta_baja === 'no'" placeholder="Describe cualquier limitación de acceso, restricciones de movimiento, o condiciones especiales..." class="w-full px-3 py-2 rounded border-2 border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 text-sm font-semibold text-gray-800"></textarea>
                            <p class="text-xs text-red-600 mt-1">Este campo es obligatorio para documentar accesibilidad</p>
                        </div>
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
</main>

<script>
    function appArticulos() {
        return {
            materiales: @json($materiales),
            articulos_catalogo: @json($articulos),
            proyecto_id: '',
            form: {
                id_articulo_produccion: '',
                material_id: '',
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
                usa_melamina: false,
                usa_textil: false,
                usa_herreria: false,
                usa_cubierta: false,
                tipo_madera: '',
                tipo_melamina: '',
                tipo_tela: '',
                tipo_cubierta: '',
                requiere_instalacion: false,
                requiere_desemplaye: false,
                comentarios: '',
                es_planta_baja: 'si',
                condiciones_acceso: ''
            },
            imagePreview: null,
            articulos: [],
            showModalMateriales: false,
            tipoMaterialSeleccionado: null,
            showModalEdicion: false,
            indexEdicion: null,
            formularioMaterial: {
                nombre: '',
                imagen: null,
                imagenPreview: null
            },
            
            // Cálculo matemático automático
            calcularCubicaje() {
                const h = parseFloat(this.form.alto) || 0;
                const w = parseFloat(this.form.ancho) || 0;
                const d = parseFloat(this.form.profundo) || 0;
                this.form.cubicaje = (h * w * d).toFixed(3);
            },
            
            get articulosFiltrados() {
                if (!this.form.material_id) {
                    return [];
                }
                return this.articulos_catalogo.filter(articulo => articulo.material_id == this.form.material_id);
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

            agregarArticulo() {
                // Validación básica
                if(!this.form.nombre || !this.form.material_id) {
                    alert('Faltan datos obligatorios (Categoría o Nombre)');
                    return;
                }

                const nuevoItem = JSON.parse(JSON.stringify(this.form));
                nuevoItem.imagen = this.imagePreview; 
                
                this.articulos.unshift(nuevoItem); 
                
                // Limpiar formulario (manteniendo algunos valores lógicos si se desea)
                this.resetForm();
            },

            eliminarArticulo(index) {
                if(confirm('¿Borrar?')) this.articulos.splice(index, 1);
            },

            resetForm() {
                this.form.material_id = '';
                this.form.nombre = '';
                this.form.id_articulo_produccion = '';
                this.form.ancho = ''; this.form.alto = ''; this.form.profundo = '';
                this.form.peso = ''; this.form.cubicaje = 0;
                this.form.descripcion = '';
                this.form.comentarios = '';
                this.form.es_planta_baja = 'si';
                this.form.condiciones_acceso = '';
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
                    imagenPreview: null
                };
            },            

            cerrarModalMateriales() {
                this.showModalMateriales = false;
                this.tipoMaterialSeleccionado = null;
                this.formularioMaterial = {
                    tipo_madera: '',
                    color_madera: '',
                    grosor_madera: '',
                    observaciones_madera: '',
                    marca_melamina: '',
                    color_melamina: '',
                    acabado_melamina: '',
                    observaciones_melamina: '',
                    tipo_tela: '',
                    color_tela: '',
                    patron_tela: '',
                    observaciones_tela: '',
                    tipo_cubierta: '',
                    material_cubierta: '',
                    especificaciones_cubierta: '',
                    observaciones_cubierta: ''
                };
            },

            seleccionarTipoMaterial(tipo) {
                this.tipoMaterialSeleccionado = tipo;
            },

            agregarMaterial() {
                // Aquí iría la lógica para guardar el nuevo material en la base de datos vía AJAX.
                // Por ahora, solo actualiza el formulario principal como en la versión anterior,
                // pero usando el nuevo campo 'nombre'.

                if (!this.formularioMaterial.nombre) {
                    alert('Por favor, ingresa el nombre del material.');
                    return;
                }

                if (this.tipoMaterialSeleccionado === 'madera') {
                    this.form.tipo_madera = this.formularioMaterial.nombre;
                    this.form.usa_madera = true;
                } else if (this.tipoMaterialSeleccionado === 'melamina') {
                    this.form.tipo_melamina = this.formularioMaterial.nombre;
                    this.form.usa_melamina = true;
                } else if (this.tipoMaterialSeleccionado === 'tela') {
                    this.form.tipo_tela = this.formularioMaterial.nombre;
                    this.form.usa_textil = true;
                } else if (this.tipoMaterialSeleccionado === 'cubierta') {
                    this.form.tipo_cubierta = this.formularioMaterial.nombre;
                    this.form.usa_cubierta = true;
                }
                alert('Material "' + this.formularioMaterial.nombre + '" agregado al artículo actual.');
                this.cerrarModalMateriales();
            },

            editarArticulo(index) {
                this.indexEdicion = index;
                this.form = JSON.parse(JSON.stringify(this.articulos[index]));
                this.imagePreview = this.form.imagen || null;
                this.showModalEdicion = true;
            },

            duplicarArticulo(index) {
                this.indexEdicion = null;
                this.form = JSON.parse(JSON.stringify(this.articulos[index]));
                this.imagePreview = this.form.imagen || null;
                this.form.id_articulo_produccion = '';
                this.showModalEdicion = true;
            },

            cerrarModalEdicion() {
                this.showModalEdicion = false;
                this.indexEdicion = null;
                this.resetForm();
                this.imagePreview = null;
            },

            guardarEdicion() {
                if(!this.form.nombre || !this.form.material_id) {
                    alert('Faltan datos obligatorios (Categoría o Nombre)');
                    return;
                }

                this.form.imagen = this.imagePreview;
                
                if (this.indexEdicion !== null) {
                    // Modo edición: actualizar artículo existente
                    this.articulos[this.indexEdicion] = JSON.parse(JSON.stringify(this.form));
                    alert('Artículo actualizado correctamente');
                } else {
                    // Modo duplicación: agregar como nuevo artículo
                    this.articulos.unshift(JSON.parse(JSON.stringify(this.form)));
                    alert('Artículo duplicado correctamente');
                }
                
                this.cerrarModalEdicion();
            }
        }
    }
</script>

@endsection
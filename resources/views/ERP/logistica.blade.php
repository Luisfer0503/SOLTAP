@extends('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="logisticaApp()">

    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="ph ph-truck text-blue-600 mr-2"></i> Logística y Entregas
                </h2>
            </div>
            
            <div class="bg-gray-100 p-1 rounded-lg flex space-x-1">
                <button @click="tab = 'entregas'" 
                        :class="tab === 'entregas' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-bold transition flex items-center">
                    <i class="ph ph-package mr-2"></i> Planificación de Entrega
                </button>
                <button @click="tab = 'retornos'" 
                        :class="tab === 'retornos' ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-4 py-2 rounded-md text-sm font-bold transition flex items-center">
                    <i class="ph ph-arrow-u-up-left mr-2"></i> Gestión de Retornos
                </button>
            </div>
        </div>

        <div class="relative w-full md:w-96">
            <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
            <input type="text" 
                   x-model="searchProyecto" 
                   placeholder="Buscar proyecto por nombre o cliente..." 
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            
            <div x-show="searchProyecto" @click.away="searchProyecto = ''" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-64 overflow-y-auto">
                <template x-for="proyecto in filteredProyectos" :key="proyecto.id">
                    <button @click="selectProyecto(proyecto); searchProyecto = ''" 
                            class="w-full px-4 py-3 text-left hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="font-bold text-sm text-gray-800" x-text="proyecto.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="proyecto.cliente"></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                  :class="proyecto.estatus === 'Activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                  x-text="proyecto.estatus"></span>
                        </div>
                    </button>
                </template>
                <div x-show="filteredProyectos.length === 0" class="px-4 py-3 text-center text-sm text-gray-500">
                    No se encontraron proyectos
                </div>
            </div>
        </div>

        <p class="text-sm text-gray-500 mt-3">Proyecto: <span class="font-bold text-gray-700" x-text="proyectoActual.nombre"></span> • <span class="text-gray-600" x-text="'Cliente: ' + proyectoActual.cliente"></span></p>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        
        <div x-show="tab === 'entregas'" x-transition:enter="transition ease-out duration-300">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">
                            Datos de Sitio
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 mb-1">Dirección de Entrega</label>
                            <p class="text-sm text-gray-800 font-medium flex items-start">
                                <i class="ph ph-map-pin text-gray-400 mr-2 mt-0.5"></i>
                                {{ $proyecto->direccion }}
                            </p>
                        </div>

                        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <label class="block text-xs font-bold text-gray-500 mb-2">Validación de Acceso (Previa)</label>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">¿Es Planta Baja?</span>
                                @if($proyecto->es_planta_baja)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded flex items-center"><i class="ph ph-check mr-1"></i> Sí</span>
                                @else
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded flex items-center"><i class="ph ph-elevator mr-1"></i> No (Requiere Elevador/Escaleras)</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-red-600 mb-1 flex items-center">
                                <i class="ph ph-warning-circle mr-1"></i> Excepciones / Condiciones de Acceso *
                            </label>
                            <textarea placeholder="Ej. El camión no entra en la calle, se requiere acarreo de 50m. Horario restringido..." 
                                      class="w-full text-sm border-red-200 focus:border-red-500 focus:ring-red-500 rounded-lg bg-red-50" 
                                      rows="4"></textarea>
                            <p class="text-[10px] text-gray-400 mt-1">Este campo es obligatorio para generar la hoja de ruta.</p>
                        </div>
                    </div>

                    <button class="w-full py-3 bg-blue-800 hover:bg-blue-900 text-white rounded-xl shadow-lg font-bold flex items-center justify-center transition">
                        <i class="ph ph-file-pdf mr-2 text-xl"></i> Generar Orden de Entrega
                    </button>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-700">Checklist de Carga y Artículos</h3>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold">{{ count($articulos) }} Ítems</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($articulos as $item)
                            <div class="p-6 hover:bg-gray-50 transition group">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mr-3">
                                            <i class="ph ph-package text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $item->nombre }}</h4>
                                            <p class="text-xs text-gray-500">{{ $item->dimensiones }} | {{ $item->peso }}kg</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        @if($item->requiere_instalacion)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded flex items-center border border-yellow-200">
                                                <i class="ph ph-wrench mr-1"></i> Instalación
                                            </span>
                                        @endif
                                        @if($item->requiere_emplaye)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded flex items-center border border-purple-200">
                                                <i class="ph ph-package mr-1"></i> Emplaye
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Notas de Producción/Ventas</p>
                                        <p class="text-xs text-gray-600 italic">
                                            {{ $item->comentarios_ventas ?: 'Sin comentarios registrados.' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-blue-600 mb-1">Observaciones Logística</label>
                                        <input type="text" placeholder="Ej. Cargar en camión pequeño, desarmar antes..." 
                                               class="w-full text-xs border-gray-300 rounded focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'retornos'" x-cloak>
            <div class="max-w-4xl mx-auto">
                
                <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-8 overflow-hidden">
                    <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex items-center">
                        <i class="ph ph-arrow-u-up-left text-orange-600 text-xl mr-2"></i>
                        <h3 class="text-lg font-bold text-orange-800">Registrar Nuevo Retorno</h3>
                    </div>
                    
                    <form class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar Artículo a Retornar</label>
                                <select class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-orange-500">
                                    <option value="">Seleccione...</option>
                                    @foreach($articulos as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }} (ID: {{ $item->id }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Destinatario (Cliente/Origen)</label>
                                <div class="relative">
                                    <i class="ph ph-user absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" placeholder="Persona que devuelve" class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Persona que Recibe (Interno)</label>
                                <div class="relative">
                                    <i class="ph ph-identification-badge absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" placeholder="Staff a cargo" class="pl-10 w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ubicación Interna (Destino)</label>
                                <select class="w-full rounded-lg border-gray-300 focus:ring-orange-500">
                                    <option>Almacén General - Nave A</option>
                                    <option>Taller de Reparaciones</option>
                                    <option>Showroom</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Motivo</label>
                                <select class="w-full rounded-lg border-gray-300 focus:ring-orange-500">
                                    <option>Defecto de Fábrica</option>
                                    <option>Daño en Traslado</option>
                                    <option>Cambio de Modelo</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" class="px-6 py-2 bg-orange-600 text-white font-bold rounded-lg shadow hover:bg-orange-700 transition flex items-center">
                                <i class="ph ph-floppy-disk mr-2"></i> Guardar Retorno
                            </button>
                        </div>
                    </form>
                </div>

                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Retornos Activos</h4>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Artículo</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Recibió</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">Silla Eames (Dañada)</td>
                                <td class="px-6 py-4 text-sm text-gray-500">Juan Almacén</td>
                                <td class="px-6 py-4 text-sm text-gray-500">10 Feb 2026</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">En Revisión</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
    function logisticaApp() {
        return {
            tab: 'entregas', // 'entregas' o 'retornos'
            searchProyecto: '',
            proyectoActual: {
                id: 1,
                nombre: 'Oficinas Corporativo Santa Fe',
                cliente: 'Tech Solutions SA',
                direccion: 'Av. Santa Fe 450, Piso 12, CDMX',
                estatus: 'Activo',
                es_planta_baja: false
            },
            proyectos: [
                {
                    id: 1,
                    nombre: 'Oficinas Corporativo Santa Fe',
                    cliente: 'Tech Solutions SA',
                    direccion: 'Av. Santa Fe 450, Piso 12, CDMX',
                    estatus: 'Activo'
                },
                {
                    id: 2,
                    nombre: 'Departamento Polanco',
                    cliente: 'Juan García Ruiz',
                    direccion: 'Blvd. Paseo de la Reforma 505, Polanco',
                    estatus: 'Activo'
                },
                {
                    id: 3,
                    nombre: 'Casa Campestre Querétaro',
                    cliente: 'María González López',
                    direccion: 'Rancho Los Alamos, Querétaro',
                    estatus: 'En Entrega'
                },
                {
                    id: 4,
                    nombre: 'Boutique Centro Histórico',
                    cliente: 'Moda Boutique SA',
                    direccion: 'Madero 25, Centro, CDMX',
                    estatus: 'Activo'
                }
            ],
            
            get filteredProyectos() {
                if (!this.searchProyecto) return this.proyectos;
                const search = this.searchProyecto.toLowerCase();
                return this.proyectos.filter(p => 
                    p.nombre.toLowerCase().includes(search) || 
                    p.cliente.toLowerCase().includes(search)
                );
            },
            
            selectProyecto(proyecto) {
                this.proyectoActual = proyecto;
                // Aquí irría la lógica para recargar los datos del proyecto seleccionado
                // window.location.href = `/logistica/${proyecto.id}`;
            }
        }
    }
</script>

@endsection
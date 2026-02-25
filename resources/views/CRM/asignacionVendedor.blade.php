@extends ('principal')

@section('contenido')

<script src="//unpkg.com/alpinejs" defer></script>

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100" x-data="asignacionApp()" @load="init()" x-init="init()">
        
        <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-handshake text-blue-600 mr-2"></i> Asignar Vendedor/Diseñador
            </h2>
            <div class="flex items-center space-x-4">
                <button type="button" @click="verAsignaciones()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center space-x-2 transition shadow-sm">
                    <i class="ph ph-list-checks text-lg"></i>
                    <span>Ver Asignaciones</span>
                </button>
                <button class="p-2 text-gray-400 hover:text-gray-600"><i class="ph ph-bell text-xl"></i></button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto">
                
                @if(session('mensaje'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center shadow-sm">
                        <i class="ph ph-check-circle text-2xl mr-2"></i>
                        <span class="font-medium">{{ session('mensaje') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center shadow-sm">
                        <i class="ph ph-warning-circle text-2xl mr-2"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                        <strong class="flex items-center"><i class="ph ph-warning mr-2"></i> Errores de validación:</strong>
                        <ul class="list-disc list-inside mt-1 ml-6">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('asignacionVendedor') }}" method="POST">
                    @csrf 

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <div class="lg:col-span-2 space-y-6">
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                    <h3 class="text-md font-bold text-gray-800 flex items-center">
                                        <i class="ph ph-user-focus mr-2 text-blue-500"></i> Selección de Prospecto/Cliente
                                    </h3>
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100">Paso 1</span>
                                </div>
                                
                                <div class="p-6">
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Prospecto a Asignar</label>
                                        <div class="relative">
                                            <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                                            <div class="relative">
                                                <input type="text" name="IdProspectoSearch" placeholder="Buscar por nombre..." x-model="searchTerm" @input="open = true" @click="open = true" @focus="open = true" class="w-full pl-10 rounded-lg border-gray-300 bg-white border px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500">
                                                <input type="hidden" name="IdProspecto" x-model="selectedProspectoId">
                                                <div class="pointer-events-none absolute left-3 top-3 text-gray-400">
                                                    <i class="ph ph-magnifying-glass"></i>
                                                </div>
                                                <ul x-show="open && filteredProspectos().length > 0" @click.away="open = false" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded shadow max-h-56 overflow-auto">
                                                    <template x-for="p in filteredProspectos()" :key="p.id">
                                                        <li @click="seleccionarProspecto(p.id); open = false;" class="px-3 py-2 hover:bg-gray-100 cursor-pointer" x-text="p.nombre_completo"></li>
                                                    </template>
                                                </ul>
                                            </div>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                <i class="ph ph-caret-down"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2">Se muestran todos los prospectos registrados.</p>
                                    </div>

                                    <div x-show="selectedProspectoId" class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                        <h4 class="text-sm font-bold text-blue-900 mb-3 flex items-center">
                                            <i class="ph ph-info text-blue-600 mr-2"></i> Información del Prospecto
                                        </h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <p class="text-xs text-blue-700 font-semibold">Nombre</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="prospectoActual.nombre"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 font-semibold">Teléfono</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="prospectoActual.telefono"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 font-semibold">Correo</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="prospectoActual.correo"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 font-semibold">Estatus</p>
                                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full" :class="prospectoActual.estatus === 'Interesado' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'" x-text="prospectoActual.estatus"></span>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-xs text-blue-700 font-semibold">Empresa</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="getProyectoSeleccionado() ? (getProyectoSeleccionado().empresa || prospectoActual.empresa) : prospectoActual.empresa"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-xs text-blue-700 font-semibold">Dirección</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="prospectoActual.direccion"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-xs text-blue-700 font-semibold">Proyecto</p>
                                                <p class="text-sm text-gray-800 font-bold" x-text="getProyectoSeleccionado() ? getProyectoSeleccionado().nombre : prospectoActual.proyecto"></p>
                                            </div>
                                            <div class="col-span-2" x-show="getProyectoSeleccionado()">
                                                <p class="text-xs text-blue-700 font-semibold">Descripción del Proyecto</p>
                                                <p class="text-sm text-gray-800" x-text="getProyectoSeleccionado()?.descripcion || 'Sin descripción'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lista de Proyectos -->
                                    <div x-show="selectedProspectoId && (proyectos.length > 0 || cargandoProyectos)" class="mb-6 p-4 bg-gray-50 border-2 border-gray-200 rounded-lg">
                                        <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center justify-between">
                                            <span class="flex items-center"><i class="ph ph-briefcase text-gray-600 mr-2"></i> Proyectos Asociados</span>
                                            <span x-show="cargandoProyectos" class="text-xs text-gray-500"><i class="ph ph-spinner animate-spin"></i> Cargando...</span>
                                        </h4>
                                        
                                        <div x-show="!cargandoProyectos && proyectos.length === 0" class="text-sm text-gray-500 italic">
                                            No se encontraron proyectos adicionales.
                                        </div>

                                        <ul x-show="!cargandoProyectos && proyectos.length > 0" class="space-y-2 max-h-40 overflow-y-auto">
                                            <template x-for="proy in proyectos" :key="proy.proyecto_id">
                                                <li class="bg-white p-2 rounded border border-gray-200 shadow-sm text-sm">
                                                    <div class="flex justify-between items-start">
                                                        <span class="font-semibold text-blue-700" x-text="proy.nombre"></span>
                                                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600 border border-gray-200" x-text="proy.estatus || 'Nuevo'"></span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="proy.descripcion || 'Sin descripción'"></p>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Asignación</label>
                                            <input type="date" name="Fecha" x-model="fechaActual" required class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2 focus:ring-blue-500" readonly >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                            <input type="time" name="Hora" x-model="horaActual" required class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2 focus:ring-blue-500" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-6">
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col">
                                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                    <h3 class="text-md font-bold text-gray-800 flex items-center">
                                        <i class="ph ph-users-three mr-2 text-green-500"></i> Proyecto y Asignación
                                    </h3>
                                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded border border-green-100">Paso 2</span>
                                </div>
                                
                                <div class="p-6 flex-1 space-y-6">

                                      <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ph ph-briefcase text-gray-400 text-lg"></i>
                                            </div>
                                            <select name="proyecto_id" x-model="selectedProyectoId" class="pl-10 block w-full rounded-lg border-gray-300 bg-white border px-3 py-2.5 focus:ring-blue-500 appearance-none">
                                                <option value="">Seleccionar Proyecto...</option>
                                                <template x-for="proy in proyectos" :key="proy.proyecto_id">
                                                    <option :value="proy.proyecto_id" x-text="proy.nombre"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Asignar Vendedor</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="ph ph-user-circle text-gray-400 text-lg"></i>
                                            </div>
                                            <select name="IdVendedor" required class="pl-10 block w-full rounded-lg border-gray-300 bg-white border px-3 py-2.5 focus:ring-blue-500 appearance-none">
                                                <option value="">Seleccionar...</option>
                                                @foreach($vendedores as $vendedor)
                                                    <option value="{{ $vendedor->vendedor_id }}">{{ $vendedor->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo de Diseño</label>
                                        <div class="flex gap-2">
                                               <select name="tiempo_id" required class="pl-10 block w-full rounded-lg border-gray-300 bg-white border px-3 py-2.5 focus:ring-blue-500 appearance-none">
                                                <option value="">Seleccionar...</option>
                                                @foreach($tiempo_disenno as $tiempo)
                                                    <option value="{{ $tiempo->tiempo_id }}">{{ $tiempo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                                <div class="p-6 border-t border-gray-100 bg-gray-50">
                                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transition flex justify-center items-center">
                                        <i class="ph ph-check-circle mr-2 text-xl"></i> Confirmar Asignación
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ver Asignaciones -->
        <div x-show="mostrarModalAsignaciones" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" @click.self="mostrarModalAsignaciones = false" style="display:none;">
            <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-4xl max-h-[80vh] overflow-hidden flex flex-col">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Asignaciones Realizadas</h3>
                    <button @click="mostrarModalAsignaciones = false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                <div class="p-0 overflow-auto flex-1">
                    <div x-show="cargandoLista" class="p-8 text-center text-gray-500">
                        <i class="ph ph-spinner animate-spin text-2xl"></i> Cargando...
                    </div>
                    <table x-show="!cargandoLista" class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600 font-medium border-b">
                            <tr>
                                <th class="px-4 py-3">Prospecto</th>
                                <th class="px-4 py-3">Proyecto</th>
                                <th class="px-4 py-3">Vendedor</th>
                                <th class="px-4 py-3">Tiempo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="asig in listaAsignaciones" :key="asig.cliente_id + '-' + asig.proyecto_id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="asig.prospecto"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="asig.proyecto"></td>
                                    <td class="px-4 py-3 text-blue-600" x-text="asig.vendedor"></td>
                                    <td class="px-4 py-3 text-gray-500" x-text="asig.tiempo"></td>
                                </tr>
                            </template>
                            <template x-if="!cargandoLista && listaAsignaciones.length === 0">
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay asignaciones registradas.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                    <button @click="mostrarModalAsignaciones = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cerrar</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<script>
    function asignacionApp() {
        return {
            selectedProspectoId: '',
            selectedProyectoId: '',
            searchTerm: '',
            open: false,
            tiempoDiseno: '',
            mostrarModalAsignaciones: false,
            listaAsignaciones: [],
            cargandoLista: false,
            proyectos: [],
            cargandoProyectos: false,
            fechaActual: '',
            horaActual: '',
            prefijoProyecto: '',
            partePersonalizada: '',
            nombreProyectoCompleto: '',
            prospectoActual: {
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                nombre_completo: '',
                telefono: '',
                correo: '',
                estatus: '',
                direccion: '',
                cliente_id: null,
                empresa: ''
            },

            // Prospectos cargados desde el servidor
            prospectos: @json($prospectos),

            init() {
                // Establecer fecha actual
                const hoy = new Date();
                const año = hoy.getFullYear();
                const mes = String(hoy.getMonth() + 1).padStart(2, '0');
                const día = String(hoy.getDate()).padStart(2, '0');
                this.fechaActual = `${año}-${mes}-${día}`;

                // Establecer hora actual
                const hora = String(hoy.getHours()).padStart(2, '0');
                const minuto = String(hoy.getMinutes()).padStart(2, '0');
                this.horaActual = `${hora}:${minuto}`;
            },

            getProyectoSeleccionado() {
                if (!this.selectedProyectoId) return null;
                return this.proyectos.find(p => p.proyecto_id == this.selectedProyectoId);
            },

            generarNombreProyecto() {
                if (!this.prospectoActual.nombre || !this.prospectoActual.apellido_paterno || !this.prospectoActual.apellido_materno || !this.prospectoActual.empresa) {
                    this.prefijoProyecto = '';
                    this.nombreProyectoCompleto = '';
                    return;
                }

                // Primeras 2 letras del nombre
                const dosLetrasNombre = this.prospectoActual.nombre.substring(0, 2).toUpperCase();
                
                // Primeras 2 letras del apellido paterno
                const dosApellidoPaterno = this.prospectoActual.apellido_paterno.substring(0, 2).toUpperCase();
                
                // Primeras 2 letras del apellido materno
                const dosApellidoMaterno = this.prospectoActual.apellido_materno.substring(0, 2).toUpperCase();
                
                // Código de empresa
                let codigoEmpresa = '';
                if (this.prospectoActual.empresa.toLowerCase().includes('casa tapier')) {
                    codigoEmpresa = 'CT';
                } else if (this.prospectoActual.empresa.toLowerCase().includes('solferino')) {
                    codigoEmpresa = 'SF';
                } else {
                    // Si no coincide, usar las primeras 2 letras
                    codigoEmpresa = this.prospectoActual.empresa.substring(0, 2).toUpperCase();
                }
                
                // Generar prefijo automático: 2 nombre + 2 apellido paterno + 2 apellido materno + guion + empresa
                const prefijoAutomatico = `${dosLetrasNombre}${dosApellidoPaterno}${dosApellidoMaterno}-${codigoEmpresa}`;
                
                // Actualizar el prefijo (read-only)
                this.prefijoProyecto = prefijoAutomatico;
                
                // Combinar prefijo + parte personalizada
                this.nombreProyectoCompleto = prefijoAutomatico + this.partePersonalizada;
            },

            filteredProspectos() {
                if (!this.searchTerm) return this.prospectos;
                return this.prospectos.filter(p => p.nombre_completo.toLowerCase().includes(this.searchTerm.toLowerCase()));
            },

            async seleccionarProspecto(id) {
                this.selectedProspectoId = id;
                this.selectedProyectoId = ''; // Resetear proyecto seleccionado al cambiar prospecto
                const prospecto = this.prospectos.find(p => p.id == id);
                if (prospecto) {
                    this.prospectoActual = { ...prospecto };
                    this.searchTerm = prospecto.nombre_completo;
                    this.partePersonalizada = '';
                    this.generarNombreProyecto();
                    
                    // Cargar proyectos usando el ID del prospecto
                    this.proyectos = [];
                    this.cargandoProyectos = true;
                    try {
                        const res = await fetch(`{{ url('crm/prospectos') }}/${id}/proyectos`);
                        if (res.ok) {
                            this.proyectos = await res.json();
                        }
                    } catch (e) {
                        console.error('Error cargando proyectos', e);
                    } finally {
                        this.cargandoProyectos = false;
                    }
                }
            },

            cargarProspecto() {
                if (!this.selectedProspectoId) {
                    this.prospectoActual = {
                        nombre: '',
                        apellido_paterno: '',
                        apellido_materno: '',
                        nombre_completo: '',
                        telefono: '',
                        correo: '',
                        estatus: '',
                        direccion: '',
                        cliente_id: null,
                        empresa: ''
                    };
                    this.prefijoProyecto = '';
                    this.partePersonalizada = '';
                    this.nombreProyectoCompleto = '';
                    return;
                }
                const prospecto = this.prospectos.find(p => p.id == this.selectedProspectoId);
                if (prospecto) {
                    this.prospectoActual = { ...prospecto };
                    this.generarNombreProyecto();
                }
            },

            async verAsignaciones() {
                this.mostrarModalAsignaciones = true;
                this.cargandoLista = true;
                try {
                    const res = await fetch('{{ route("listarAsignaciones") }}');
                    if (res.ok) {
                        this.listaAsignaciones = await res.json();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.cargandoLista = false;
                }
            }
        }
    }
</script>

@endsection

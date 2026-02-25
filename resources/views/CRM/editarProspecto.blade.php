@extends('principal')

@section('contenido')

    <style>
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

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100">
        
        <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-pencil text-blue-600 mr-2"></i> Editar Prospecto
            </h2>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-gray-400 hover:text-gray-600"><i class="ph ph-bell text-xl"></i></button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-6xl mx-auto">
                
                @if(session('mensaje'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('mensaje') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <strong>Errores de validación:</strong>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('actualizarProspecto', $prospecto->prospecto_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-identification-card mr-2 text-blue-500"></i> Datos Personales
                            </h3>
                                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                                <input type="text" name="idProspecto" value="{{ $prospecto->prospecto_id }}"  readonly required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" name="Nombre" value="{{ old('Nombre', $prospecto->nombre) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                                <input type="text" name="ApellidoPat" value="{{ old('ApellidoPat', $prospecto->apellido_paterno) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                                <input type="text" name="ApellidoMat" value="{{ old('ApellidoMat', $prospecto->apellido_materno) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden" x-data="ubicacionForm()">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-map-pin mr-2 text-blue-500"></i> Contacto y Ubicación
                            </h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <div class="relative">
                                    <i class="ph ph-envelope absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="email" name="Correo" value="{{ old('Correo', $prospecto->correo) }}" required class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <div class="relative">
                                    <i class="ph ph-phone absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="number" name="Telefono" value="{{ old('Telefono', $prospecto->telefono) }}" required class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                                <select name="IdEmpresa" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Empresa...</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->empresa_id }}" @selected(old('IdEmpresa', $prospecto->empresa_id) == $empresa->empresa_id)>{{ $empresa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="IdEstado" x-on:change="actualizarEstado($event)" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Estado...</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->estado_id }}" data-nombre="{{ $estado->nombre }}" @selected(old('IdEstado', $prospecto->estado_id) == $estado->estado_id)>{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                                <input type="text" name="Municipio" x-model="municipio" @change="generarMapsUrl()" value="{{ old('Municipio', $prospecto->municipio) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
                                <input type="text" name="Calle" x-model="calle" @change="generarMapsUrl()" value="{{ old('Calle', $prospecto->calle) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps (URL o Coordenadas)</label>
                                <div class="relative">
                                    <i class="ph ph-google-logo absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" name="Maps" x-model="mapsUrl" value="{{ old('Maps', $prospecto->maps) }}" class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="https://maps.google.com/..."  readonly >
                                </div>
                            </div>

                            <!-- Radio botón: ¿Dirección de entrega diferente? -->
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3">¿La dirección de entrega es diferente?</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="DireccionDiferente" value="no" x-model="direccionDiferente" class="w-4 h-4 text-blue-600 cursor-pointer">
                                        <span class="ml-2 text-sm text-gray-700">No, es la misma</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="DireccionDiferente" value="si" x-model="direccionDiferente" class="w-4 h-4 text-blue-600 cursor-pointer">
                                        <span class="ml-2 text-sm text-gray-700">Sí, es diferente</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Campo de dirección de entrega (solo si es diferente) -->
                            <div class="md:col-span-3" x-show="direccionDiferente === 'si'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Entrega</label>
                                <input type="text" name="DireccionEntrega" x-model="direccionEntrega" value="{{ old('DireccionEntrega', $prospecto->direccion_entrega) }}" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="Ingresa la dirección de entrega diferente...">
                                <p class="text-xs text-gray-500 mt-1">Completa este campo si la dirección de entrega es distinta a la ubicación del prospecto.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden" x-data="proyectoForm()" x-init="init()">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-chart-line-up mr-2 text-blue-500"></i> Clasificación y Proyecto
                            </h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" name="Fecha" readonly class="w-full rounded-lg border-gray-300 bg-gray-100 border px-3 py-2" value="{{ old('Fecha', \Carbon\Carbon::parse($prospecto->fecha)->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                <input type="time" name="Hora" readonly class="w-full rounded-lg border-gray-300 bg-gray-100 border px-3 py-2" value="{{ old('Hora', \Carbon\Carbon::parse($prospecto->fecha)->format('H:i')) }}">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proyecto</label>
                                <div class="flex gap-2 items-center">
                                    <input type="text" x-model="prefijoProyecto" readonly class="flex-shrink-0 rounded-lg border-gray-300 bg-gray-100 border px-3 py-2 font-semibold text-gray-700 w-40" placeholder="Auto-generado">
                                    <input type="text" name="NombreProyecto" x-model="partePersonalizada" value="{{ old('NombreProyecto', $prospecto->proyecto) }}" class="flex-1 rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="Escribe aquí..." maxlength="50">
                                </div>
                                <input type="hidden" name="NombreProyectoCompleto" x-model="nombreProyectoCompleto">
                                <p class="text-xs text-gray-500 mt-1">La nomenclatura está protegida. Solo escribe en el segundo campo.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Enfoque</label>
                                <select name="IdEnfoque" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                                    <option value="">Seleccionar Enfoque</option>
                                    @foreach($enfoques as $enfoque)
                                        <option value="{{ $enfoque->enfoque_id }}" @selected(old('IdEnfoque', $prospecto->enfoque_id) == $enfoque->enfoque_id)>{{ $enfoque->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Canal Distribuccion</label>
                                <select name="IdCanalDistribuccion" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                                    <option value="">Seleccionar Canal...</option>
                                    @foreach($canales as $canal)
                                        <option value="{{ $canal->canal_id }}" @selected(old('IdCanalDistribuccion', $prospecto->canal_id) == $canal->canal_id)>{{ $canal->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
                                <select name="IdEstatus" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Estatus...</option>
                                    @foreach($estatus as $est)
                                        <option value="{{ $est->estatus_id }}" @selected(old('IdEstatus', $prospecto->estatus_id) == $est->estatus_id)>{{ $est->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción / Notas</label>
                                <textarea name="Descripcion" rows="3" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="Detalles del prospecto...">{{ old('Descripcion', $prospecto->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pb-8">
                        <a href="{{ route('altaProspectos') }}" class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 font-medium shadow-lg transition flex items-center">
                            <i class="ph ph-floppy-disk mr-2 text-xl"></i> Actualizar Prospecto
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script>
        function ubicacionForm() {
            return {
                estadoNombre: '',
                municipio: '{{ $prospecto->municipio }}',
                calle: '{{ $prospecto->calle }}',
                mapsUrl: '{{ $prospecto->maps }}',
                direccionDiferente: '{{ strpos($prospecto->direccion_entrega, $prospecto->calle) !== false ? "no" : "si" }}',
                direccionEntrega: '{{ $prospecto->direccion_entrega }}',
                
                actualizarEstado(event) {
                    const selectedOption = event.target.options[event.target.selectedIndex];
                    this.estadoNombre = selectedOption.getAttribute('data-nombre');
                    this.generarMapsUrl();
                },
                
                generarMapsUrl() {
                    if (this.estadoNombre && this.municipio && this.calle) {
                        const direccion = `${this.calle}, ${this.municipio}, ${this.estadoNombre}, Mexico`;
                        this.mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(direccion)}`;
                    }
                }
            }
        }

        function proyectoForm() {
            return {
                nombreProyecto: '',
                prefijoProyecto: '',
                partePersonalizada: '{{ $prospecto->proyecto }}',
                nombreProyectoCompleto: '{{ $prospecto->proyecto }}',
                nombre: '{{ $prospecto->nombre }}',
                apellidoPat: '{{ $prospecto->apellido_paterno }}',
                apellidoMat: '{{ $prospecto->apellido_materno }}',
                empresaId: '{{ $prospecto->empresa_id }}',
                empresas: @json($empresas),

                init() {
                    this.generarNombreProyecto();
                },

                generarNombreProyecto() {
                    if (!this.nombre || !this.apellidoPat || !this.apellidoMat || !this.empresaId) {
                        this.prefijoProyecto = '';
                        this.nombreProyectoCompleto = '';
                        return;
                    }

                    const dosLetrasNombre = this.nombre.substring(0, 2).toUpperCase();
                    const dosApellidoPaterno = this.apellidoPat.substring(0, 2).toUpperCase();
                    const dosApellidoMaterno = this.apellidoMat.substring(0, 2).toUpperCase();
                    
                    let codigoEmpresa = '';
                    const empresa = this.empresas.find(e => e.empresa_id == this.empresaId);
                    if (empresa) {
                        if (empresa.nombre.toLowerCase().includes('casa tapier')) {
                            codigoEmpresa = 'CT';
                        } else if (empresa.nombre.toLowerCase().includes('solferino')) {
                            codigoEmpresa = 'SF';
                        } else {
                            codigoEmpresa = empresa.nombre.substring(0, 2).toUpperCase();
                        }
                    }
                    
                    const prefijoAutomatico = `${dosLetrasNombre}${dosApellidoPaterno}${dosApellidoMaterno}-${codigoEmpresa}`;
                    this.prefijoProyecto = prefijoAutomatico;
                    this.nombreProyectoCompleto = prefijoAutomatico + (this.partePersonalizada || '');
                }
            }
        }
    </script>

</body>
</html>
@stop

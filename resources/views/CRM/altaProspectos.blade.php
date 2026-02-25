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

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100" x-data="gestionProspectos()" x-cloak>
        
        <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-user-plus text-blue-600 mr-2"></i> Nuevo Prospecto
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('reporteEstatus') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center space-x-2 transition">
                    <i class="ph ph-chart-bar text-lg"></i>
                    <span>Reporte Estatus</span>
                </a>
                <button type="button" @click="abrirListaProspectos()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center space-x-2 transition">
                    <i class="ph ph-list text-lg"></i>
                    <span>Ver Prospectos</span>
                </button>
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
                
                <form action="{{ route('guardarProspecto') }}" method="POST">
                    @csrf 

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-identification-card mr-2 text-blue-500"></i> Datos Personales
                            </h3>
                                <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                                <input type="text" name="idProspecto" value="{{ $sigue }}"  readonly required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" name="Nombre" value="{{ old('Nombre') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                                <input type="text" name="ApellidoPat" value="{{ old('ApellidoPat') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                                <input type="text" name="ApellidoMat" value="{{ old('ApellidoMat') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden" x-data="ubicacionForm()" x-init="init()">
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
                                    <input type="email" name="Correo" value="{{ old('Correo') }}" required class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <div class="relative">
                                    <i class="ph ph-phone absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="number" name="Telefono" value="{{ old('Telefono') }}" required class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                                <select name="IdEmpresa" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Empresa...</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->empresa_id }}" @selected(old('IdEmpresa') == $empresa->empresa_id)>{{ $empresa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="IdEstado" x-on:change="actualizarEstado($event)" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Estado...</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->estado_id }}" data-nombre="{{ $estado->nombre }}" @selected(old('IdEstado') == $estado->estado_id)>{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                                <input type="text" name="Municipio" x-model="municipio" @change="generarMapsUrl()" value="{{ old('Municipio') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                                <input type="number" name="CodigoPostal" x-model="codigoPostal" @change="generarMapsUrl()" value="{{ old('CodigoPostal') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
                                <input type="text" name="Calle" x-model="calle" @change="generarMapsUrl()" value="{{ old('Calle') }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps (URL o Coordenadas)</label>
                                <div class="relative">
                                    <i class="ph ph-google-logo absolute left-3 top-2.5 text-gray-400"></i>
                                    <input type="text" name="Maps" x-model="mapsUrl" value="{{ old('Maps') }}" class="pl-9 w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="https://maps.google.com/..."  readonly >
                                </div>
                            </div>

                            <!-- Radio botón: ¿Dirección de entrega diferente? -->
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3">¿La dirección de entrega es diferente?</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="DireccionDiferente" value="no" x-model="direccionDiferente" class="w-4 h-4 text-blue-600 cursor-pointer" @change="$watch('direccionDiferente')">
                                        <span class="ml-2 text-sm text-gray-700">No, es la misma</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="DireccionDiferente" value="si" x-model="direccionDiferente" class="w-4 h-4 text-blue-600 cursor-pointer">
                                        <span class="ml-2 text-sm text-gray-700">Sí, es diferente</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Campos de dirección de entrega (solo si es diferente) -->
                            <div class="md:col-span-3" x-show="direccionDiferente === 'si'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Dirección de Entrega (detallada)</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">Estado</label>
                                        <select x-model="direccionEstadoEntrega" @change="generarMapsEntrega()" name="IdEstadoEntrega" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2">
                                            <option value="">Seleccionar Estado...</option>
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado->estado_id }}" data-nombre="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Municipio</label>
                                        <input type="text" x-model="direccionMunicipioEntrega" @input="generarMapsEntrega()" name="MunicipioEntrega" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2" placeholder="Municipio...">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Calle / Dirección</label>
                                        <input type="text" x-model="direccionCalleEntrega" @input="generarMapsEntrega()" name="CalleEntrega" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2" placeholder="Calle, número...">
                                    </div>
                                </div>
                                <input type="hidden" name="DireccionEntrega" x-model="direccionEntregaHidden">
                                <p class="text-xs text-gray-500 mt-1">Completa estos campos si la dirección de entrega es distinta a la ubicación del prospecto. El campo Google Maps se actualizará automáticamente.</p>
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
                                <input type="date" name="Fecha" x-model="fechaActual" readonly class="w-full rounded-lg border-gray-300 bg-gray-100 border px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                <input type="time" name="Hora" x-model="horaActual" readonly class="w-full rounded-lg border-gray-300 bg-gray-100 border px-3 py-2">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proyecto</label>
                                <div class="flex gap-2 items-center">
                                    <input type="text" x-model="prefijoProyecto" readonly class="flex-shrink-0 rounded-lg border-gray-300 bg-gray-100 border px-3 py-2 font-semibold text-gray-700 w-40" placeholder="Auto-generado">
                                    <input type="text" name="NombreProyecto" x-model="partePersonalizada" value="{{ old('NombreProyecto') }}" class="flex-1 rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="Escribe aquí..." maxlength="50">
                                </div>
                                <input type="hidden" name="NombreProyectoCompleto" x-model="nombreProyectoCompleto">
                                <p class="text-xs text-gray-500 mt-1">La nomenclatura está protegida. Solo escribe en el segundo campo.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Enfoque</label>
                                <select name="IdEnfoque" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                                    <option value="">Seleccionar Enfoque</option>
                                    @foreach($enfoques as $enfoque)
                                        <option value="{{ $enfoque->enfoque_id }}" @selected(old('IdEnfoque') == $enfoque->enfoque_id)>{{ $enfoque->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Interacción</label>
                                <select name="IdInteraccion" required class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2 focus:ring-blue-500">
                                    <option value="">Seleccionar Interacción...</option>
                                    @foreach($interacciones as $interaccion)
                                        <option value="{{ $interaccion->interaccion_id }}" @selected(old('IdInteraccion') == $interaccion->interaccion_id)>{{ $interaccion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción / Notas</label>
                                <textarea name="Descripcion" rows="3" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2" placeholder="Detalles iniciales del prospecto...">{{ old('Descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pb-8">
                        <button type="button" class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-8 py-3 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 font-medium shadow-lg transition flex items-center">
                            <i class="ph ph-floppy-disk mr-2 text-xl"></i> Guardar Prospecto
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <script>
        function ubicacionForm() {
            return {
                estadoNombre: '',
                municipio: '',
                codigoPostal: '',
                calle: '',
                mapsUrl: '',
                direccionDiferente: 'no',
                direccionEntrega: '',
                // guardar valor original de Maps para restaurar si el usuario deshace el cambio
                originalMaps: '',
                // campos de entrega (detalle)
                direccionEstadoEntrega: '',
                direccionMunicipioEntrega: '',
                direccionCalleEntrega: '',
                direccionEntregaHidden: '',
                // Campos de entrega detallados
                direccionEstadoEntrega: '',
                direccionMunicipioEntrega: '',
                direccionCalleEntrega: '',
                direccionEntregaHidden: '',
                
                init() {
                    // guardar valor inicial de Maps (si hay alguno)
                    const mapsInput = document.querySelector('input[name="Maps"]');
                    this.originalMaps = (mapsInput && mapsInput.value) ? mapsInput.value : '';

                    // observar si el usuario cambia la opción DireccionDiferente
                    if (this.$watch) {
                        this.$watch('direccionDiferente', (value) => {
                            if (value === 'no') {
                                // Si los campos principales están completos, regenerar Maps desde ellos
                                if (this.estadoNombre && this.municipio && this.calle && this.codigoPostal) {
                                    this.generarMapsUrl();
                                } else {
                                    // restaurar maps original si no hay datos principales
                                    this.mapsUrl = this.originalMaps;
                                }
                                this.direccionEntrega = '';
                                this.direccionEstadoEntrega = '';
                                this.direccionMunicipioEntrega = '';
                                this.direccionCalleEntrega = '';
                                this.direccionEntregaHidden = '';
                                // actualizar input DOM también
                                const mapsInput = document.querySelector('input[name="Maps"]');
                                if (mapsInput) {
                                    mapsInput.value = this.mapsUrl;
                                    mapsInput.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            }
                        });
                    }
                },

                actualizarEstado(event) {
                    const selectedOption = event.target.options[event.target.selectedIndex];
                    this.estadoNombre = selectedOption.getAttribute('data-nombre');
                    this.generarMapsUrl();
                },
                
                generarMapsUrl() {
                    if (this.estadoNombre && this.municipio && this.calle && this.codigoPostal) {
                        const direccion = `${this.calle}, ${this.codigoPostal}, ${this.municipio}, ${this.estadoNombre}, Mexico`;
                        this.mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(direccion)}`;
                    }
                },

                generarMapsEntrega() {
                    // obtener nombre del estado desde el select de entrega
                    const select = document.querySelector('select[name="IdEstadoEntrega"]');
                    let estadoNombre = '';
                    if (select && select.options[select.selectedIndex]) {
                        estadoNombre = select.options[select.selectedIndex].getAttribute('data-nombre') || select.options[select.selectedIndex].textContent || '';
                    }

                    // Solo actualizar maps si los tres campos están llenos
                    if (this.direccionCalleEntrega && this.direccionMunicipioEntrega && estadoNombre) {
                        const direccion = `${this.direccionCalleEntrega}, ${this.direccionMunicipioEntrega}, ${estadoNombre}, Mexico`;
                        this.mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(direccion)}`;
                        this.direccionEntregaHidden = `${this.direccionCalleEntrega}, ${this.direccionMunicipioEntrega}, ${estadoNombre}`;
                        // actualizar el input DOM principal
                        const mapsInput = document.querySelector('input[name="Maps"]');
                        if (mapsInput) {
                            mapsInput.value = this.mapsUrl;
                            mapsInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
                ,

                generarMapsEntrega() {
                    // obtener nombre del estado desde el select de entrega
                    const select = document.querySelector('select[name="IdEstadoEntrega"]');
                    let estadoNombre = '';
                    if (select && select.options[select.selectedIndex]) {
                        estadoNombre = select.options[select.selectedIndex].getAttribute('data-nombre') || select.options[select.selectedIndex].textContent || '';
                    }

                    if (this.direccionCalleEntrega && this.direccionMunicipioEntrega && estadoNombre) {
                        const direccion = `${this.direccionCalleEntrega}, ${this.direccionMunicipioEntrega}, ${estadoNombre}, Mexico`;
                        this.mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(direccion)}`;
                        this.direccionEntregaHidden = `${this.direccionCalleEntrega}, ${this.direccionMunicipioEntrega}, ${estadoNombre}`;
                    }
                }
            }
        }

        function proyectoForm() {
            return {
                nombreProyecto: '',
                prefijoProyecto: '',
                partePersonalizada: '',
                nombreProyectoCompleto: '',
                nombre: '',
                apellidoPat: '',
                apellidoMat: '',
                empresaId: '',
                fechaActual: '',
                horaActual: '',
                empresas: @json($empresas),

                init() {
                    // Inicializar con valores del formulario si existen
                    const nombreInput = document.querySelector('input[name="Nombre"]');
                    const apellidoPatInput = document.querySelector('input[name="ApellidoPat"]');
                    const apellidoMatInput = document.querySelector('input[name="ApellidoMat"]');
                    const empresaSelect = document.querySelector('select[name="IdEmpresa"]');
                    const parteInput = document.querySelector('input[name="NombreProyecto"]');

                    // Fecha y hora actuales (cliente)
                    const ahora = new Date();
                    const año = ahora.getFullYear();
                    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
                    const día = String(ahora.getDate()).padStart(2, '0');
                    this.fechaActual = `${año}-${mes}-${día}`;
                    const hora = String(ahora.getHours()).padStart(2, '0');
                    const minuto = String(ahora.getMinutes()).padStart(2, '0');
                    this.horaActual = `${hora}:${minuto}`;

                    if (nombreInput) {
                        this.nombre = nombreInput.value;
                        nombreInput.addEventListener('input', (e) => {
                            this.nombre = e.target.value;
                            this.generarNombreProyecto();
                        });
                    }

                    if (apellidoPatInput) {
                        this.apellidoPat = apellidoPatInput.value;
                        apellidoPatInput.addEventListener('input', (e) => {
                            this.apellidoPat = e.target.value;
                            this.generarNombreProyecto();
                        });
                    }

                    if (apellidoMatInput) {
                        this.apellidoMat = apellidoMatInput.value;
                        apellidoMatInput.addEventListener('input', (e) => {
                            this.apellidoMat = e.target.value;
                            this.generarNombreProyecto();
                        });
                    }

                    if (empresaSelect) {
                        this.empresaId = empresaSelect.value;
                        empresaSelect.addEventListener('change', (e) => {
                            this.empresaId = e.target.value;
                            this.generarNombreProyecto();
                        });
                    }

                    if (parteInput) {
                        this.partePersonalizada = parteInput.value || '';
                        parteInput.addEventListener('input', (e) => {
                            this.partePersonalizada = e.target.value;
                            this.nombreProyectoCompleto = this.prefijoProyecto + this.partePersonalizada;
                        });
                    }

                    // Generar el nombre inicial
                    this.generarNombreProyecto();
                },

                generarNombreProyecto() {
                    if (!this.empresaId) {
                        this.prefijoProyecto = '';
                        this.nombreProyectoCompleto = '';
                        return;
                    }
                    
                    // Código de empresa
                    let codigoEmpresa = '';
                    const empresa = this.empresas.find(e => e.empresa_id == this.empresaId);
                    if (empresa) {
                        if (empresa.nombre.toLowerCase().includes('casa tapier')) {
                            codigoEmpresa = 'CT';
                        } else if (empresa.nombre.toLowerCase().includes('solferino')) {
                            codigoEmpresa = 'SH';
                        } else {
                            codigoEmpresa = empresa.nombre.substring(0, 2).toUpperCase();
                        }
                    }
                    
                    // Generar prefijo automático: solo código de empresa
                    const prefijoAutomatico = `${codigoEmpresa}-`;
                    
                    // Actualizar el prefijo (read-only)
                    this.prefijoProyecto = prefijoAutomatico;
                    
                    // Combinar prefijo + parte personalizada
                    this.nombreProyectoCompleto = prefijoAutomatico + (this.partePersonalizada || '');
                }
            }
        }

        function gestionProspectos() {
            return {
                mostrarModal: false,
                prospectos: [],
                cargando: false,
                // Clientes
                mostrarModalClientes: false,
                clientes: [],
                cargandoClientes: false,
                // Empresas (para código)
                empresas: @json($empresas),
                // Estados (para generar maps desde modal)
                estados: @json($estados),
                // Proyecto modal fields
                prefijoProyectoCliente: '',
                partePersonalizadaCliente: '',
                nombreProyectoCompletoCliente: '',
                // Direccion cambio en modal clientes
                direccionCambioCliente: 'no',
                direccionEntregaCliente: '',
                // Campos de direccion modal separados
                direccionEstadoCliente: '',
                direccionMunicipioCliente: '',
                direccionCalleCliente: '',
                // Cliente seleccionado en modal
                clienteSeleccionado: null,
                // Mensajes UI
                mensajeProyecto: '',
                errorProyecto: '',
                
                async abrirListaProspectos() {
                    console.log('Abriendo lista de prospectos...');
                    this.cargando = true;
                    try {
                        const url = '{{ route("listarProspectos") }}';
                        console.log('URL:', url);
                        const response = await fetch(url);
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Response error:', errorText);
                            throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                        }
                        
                        const data = await response.json();
                        console.log('Datos recibidos:', data);
                        console.log('Cantidad de prospectos:', data.length);
                        this.prospectos = data;
                        this.mostrarModal = true;
                        console.log('Modal abierto, prospectos cargados:', this.prospectos.length);
                    } catch (error) {
                        console.error('Error detallado:', error);
                        alert('Error al cargar la lista de prospectos: ' + error.message);
                    } finally {
                        this.cargando = false;
                    }
                },
                
                async abrirClientes() {
                    console.log('Abriendo lista de clientes...');
                    this.cargandoClientes = true;
                    try {
                        const url = '{{ route("listarClientes") }}';
                        const response = await fetch(url);
                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                        }
                        const data = await response.json();
                        this.clientes = data;
                        this.mostrarModalClientes = true;
                        // reset project modal fields
                        this.prefijoProyectoCliente = '';
                        this.partePersonalizadaCliente = '';
                        this.nombreProyectoCompletoCliente = '';
                        console.log('Clientes cargados:', this.clientes.length);
                    } catch (error) {
                        console.error('Error al cargar clientes:', error);
                        alert('Error al cargar la lista de clientes: ' + error.message);
                    } finally {
                        this.cargandoClientes = false;
                    }
                },

                cerrarModalClientes() {
                    this.mostrarModalClientes = false;
                    this.mensajeProyecto = '';
                    this.errorProyecto = '';
                },

                generarMapsDesdeModal() {
                    if (!this.direccionCalleCliente || !this.direccionMunicipioCliente || !this.direccionEstadoCliente) return;
                    const estadoObj = this.estados.find(e => e.estado_id == this.direccionEstadoCliente);
                    const estadoNombre = estadoObj ? estadoObj.nombre : '';
                    const direccion = `${this.direccionCalleCliente}, ${this.direccionMunicipioCliente}, ${estadoNombre}, Mexico`;
                    const mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(direccion)}`;
                    // Actualizar el input principal de Maps (está dentro de ubicacionForm)
                    const mapsInput = document.querySelector('input[name="Maps"]');
                    if (mapsInput) {
                        mapsInput.value = mapsUrl;
                        mapsInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    // También actualizar el campo oculto direccion_entrega para enviar si procede
                    this.direccionEntregaCliente = `${this.direccionCalleCliente}, ${this.direccionMunicipioCliente}, ${estadoNombre}`;
                },

                async guardarProyectoAjax() {
                    this.mensajeProyecto = '';
                    this.errorProyecto = '';
                    if (!this.clienteSeleccionado) {
                        this.errorProyecto = 'Selecciona un cliente.';
                        return;
                    }
                    if (!this.nombreProyectoCompletoCliente) {
                        this.errorProyecto = 'Nombre del proyecto vacío.';
                        return;
                    }

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const url = '{{ route("guardarProyecto") }}';
                        const formData = new FormData();
                        formData.append('cliente_id', this.clienteSeleccionado);
                        formData.append('nombre_proyecto', this.nombreProyectoCompletoCliente);
                        formData.append('cambiar_direccion', this.direccionCambioCliente || 'no');
                        if (this.direccionCambioCliente === 'si') formData.append('direccion_entrega', this.direccionEntregaCliente || '');

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: formData
                        });

                        if (!response.ok) {
                            const errorJson = await response.json().catch(() => null);
                            const msg = (errorJson && errorJson.error) ? errorJson.error : `HTTP ${response.status}`;
                            this.errorProyecto = 'Error al guardar proyecto: ' + msg;
                            return;
                        }

                        const data = await response.json().catch(() => null);
                        this.mensajeProyecto = (data && data.mensaje) ? data.mensaje : 'Proyecto creado correctamente';
                        // limpiar campos
                        this.partePersonalizadaCliente = '';
                        this.nombreProyectoCompletoCliente = '';
                        this.prefijoProyectoCliente = '';
                        this.direccionCambioCliente = 'no';
                        this.direccionEntregaCliente = '';

                        // recargar clientes por si hay cambios
                        await this.abrirClientes();

                        // cerrar modal después de 1.2s
                        setTimeout(() => { this.cerrarModalClientes(); }, 1200);
                    } catch (err) {
                        console.error('Error AJAX guardarProyecto:', err);
                        this.errorProyecto = 'Error de red al guardar proyecto.';
                    }
                },

                generarNombreProyectoCliente(clienteId) {
                    if (!clienteId) {
                        this.prefijoProyectoCliente = '';
                        this.nombreProyectoCompletoCliente = '';
                        return;
                    }
                    const cliente = this.clientes.find(c => c.id == clienteId);
                    if (!cliente) {
                        this.prefijoProyectoCliente = '';
                        this.nombreProyectoCompletoCliente = '';
                        return;
                    }

                    const nombre = (cliente.nombre || '').toString();
                    const apellidoPat = (cliente.apellido_paterno || '').toString();
                    const apellidoMat = (cliente.apellido_materno || '').toString();
                    const empresaId = cliente.empresa_id || null;

                    const dosLetrasNombre = nombre.substring(0,2).toUpperCase();
                    const dosApellidoPaterno = apellidoPat.substring(0,2).toUpperCase();
                    const dosApellidoMaterno = apellidoMat.substring(0,2).toUpperCase();

                    let codigoEmpresa = '';
                    if (empresaId) {
                        const emp = this.empresas.find(e => e.empresa_id == empresaId);
                        if (emp) {
                            const ename = (emp.nombre || '').toLowerCase();
                            if (ename.includes('casa tapier')) codigoEmpresa = 'CT';
                            else if (ename.includes('solferino')) codigoEmpresa = 'SF';
                            else codigoEmpresa = emp.nombre.substring(0,2).toUpperCase();
                        }
                    }

                    const prefijo = `${dosLetrasNombre}${dosApellidoPaterno}${dosApellidoMaterno}-${codigoEmpresa}-`;
                    this.prefijoProyectoCliente = prefijo;
                    this.nombreProyectoCompletoCliente = prefijo + (this.partePersonalizadaCliente || '');
                },

                actualizarParteProyectoCliente() {
                    this.nombreProyectoCompletoCliente = this.prefijoProyectoCliente + (this.partePersonalizadaCliente || '');
                },

                cerrarModal() {
                    this.mostrarModal = false;
                },

                editarProspecto(id) {
                    console.log('Editando prospecto:', id);
                    window.location.href = `{{ url('crm/prospecto/editar') }}/${id}`;
                },

                eliminarProspecto(id) {
                    console.log('Eliminando prospecto:', id);
                    if (confirm('¿Estás seguro de que deseas eliminar este prospecto?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ url('crm/prospecto/eliminar') }}/${id}`;
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }
        }
    </script>

    <!-- Modal para listar prospectos -->
    <div x-show="mostrarModal" x-transition class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center" @click.self="cerrarModal()" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-4xl max-h-[80vh] overflow-hidden flex flex-col">
            <!-- Header del Modal -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="ph ph-list mr-2"></i> Lista de Prospectos
                </h3>
                <button @click="cerrarModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>

            <!-- Indicador de carga -->
            <div x-show="cargando" class="flex items-center justify-center py-8">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Cargando prospectos...</p>
                </div>
            </div>

            <!-- Tabla de Prospectos -->
            <div x-show="!cargando" class="overflow-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Nombre</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Correo</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Teléfono</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Empresa</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">Estatus</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="prospecto in prospectos" :key="prospecto.id">
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-800" x-text="prospecto.nombre_completo"></td>
                                <td class="px-4 py-3 text-gray-800" x-text="prospecto.correo"></td>
                                <td class="px-4 py-3 text-gray-800" x-text="prospecto.telefono"></td>
                                <td class="px-4 py-3 text-gray-800" x-text="prospecto.empresa"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full" 
                                        :class="prospecto.estatus === 'Cliente' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                                        x-text="prospecto.estatus">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center flex gap-1 justify-center">
                                    <button @click="editarProspecto(prospecto.id)" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition" title="Editar">
                                        <i class="ph ph-pencil"></i>
                                    </button>
                                    <button @click="eliminarProspecto(prospecto.id)" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold transition" title="Eliminar">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="prospectos.length === 0 && !cargando">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    No hay prospectos registrados
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Footer del Modal -->
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-2">
                <button @click="cerrarModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal para listar clientes y crear proyecto -->
    <div x-show="mostrarModalClientes" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" @click.self="cerrarModalClientes()" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-3xl max-h-[80vh] overflow-auto flex flex-col">
            <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center"><i class="ph ph-people mr-2"></i> Clientes</h3>
                <button @click="cerrarModalClientes()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>

            <div class="p-4">
                <div x-show="cargandoClientes" class="py-6 text-center">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                    <p class="mt-2 text-gray-600">Cargando clientes...</p>
                </div>

                <div x-show="!cargandoClientes">
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Lista de clientes</h4>
                        <ul class="divide-y">
                            <template x-for="cliente in clientes" :key="cliente.id">
                                <li class="py-2 text-gray-700">
                                    <span x-text="cliente.nombre_completo"></span>
                                </li>
                            </template>
                            <template x-if="clientes.length === 0">
                                <li class="py-4 text-center text-gray-500">No hay clientes registrados</li>
                            </template>
                        </ul>
                    </div>

                    <div class="pt-4 border-t">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Agregar nuevo proyecto</h4>
                        <form x-on:submit.prevent="guardarProyectoAjax()">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                                    <select name="cliente_id" x-model="clienteSeleccionado" @change="generarNombreProyectoCliente(clienteSeleccionado)" required class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2">
                                        <option value="">Seleccionar Cliente...</option>
                                        <template x-for="cliente in clientes" :key="cliente.id">
                                            <option :value="cliente.id" x-text="cliente.nombre_completo"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proyecto</label>
                                    <div class="flex gap-2">
                                        <input type="text" readonly x-model="prefijoProyectoCliente" class="w-40 rounded-lg border-gray-300 bg-gray-100 border px-3 py-2 font-semibold">
                                        <input type="text" x-model="partePersonalizadaCliente" @input="actualizarParteProyectoCliente()" class="flex-1 rounded-lg border-gray-300 bg-white border px-3 py-2" placeholder="Parte personalizada...">
                                    </div>
                                    <input type="hidden" name="nombre_proyecto" x-model="nombreProyectoCompletoCliente">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="cambiar_direccion" value="no" x-model="direccionCambioCliente" class="form-radio text-indigo-600" checked>
                                    <span class="ml-2 text-sm text-gray-700">No cambiar dirección de entrega</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="cambiar_direccion" value="si" x-model="direccionCambioCliente" class="form-radio text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Sí, actualizar dirección de entrega</span>
                                </label>

                                <div x-show="direccionCambioCliente === 'si'" class="mt-3 grid grid-cols-1 gap-3">
                                    <label class="block text-sm font-medium text-gray-700">Nueva Dirección de Entrega</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">Estado</label>
                                            <select x-model="direccionEstadoCliente" @change="generarMapsDesdeModal()" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2">
                                                <option value="">Seleccionar estado...</option>
                                                <template x-for="est in estados" :key="est.estado_id">
                                                    <option :value="est.estado_id" x-text="est.nombre"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Municipio</label>
                                            <input type="text" x-model="direccionMunicipioCliente" @input="generarMapsDesdeModal()" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2" placeholder="Municipio...">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">Calle / Dirección</label>
                                            <input type="text" x-model="direccionCalleCliente" @input="generarMapsDesdeModal()" class="w-full rounded-lg border-gray-300 bg-white border px-3 py-2" placeholder="Calle, número...">
                                        </div>
                                    </div>
                                    <input type="hidden" name="direccion_entrega" x-model="direccionEntregaCliente">
                                </div>

                                <template x-if="mensajeProyecto">
                                    <div class="mt-3 p-3 bg-green-100 text-green-700 rounded" x-text="mensajeProyecto"></div>
                                </template>
                                <template x-if="errorProyecto">
                                    <div class="mt-3 p-3 bg-red-100 text-red-700 rounded" x-text="errorProyecto"></div>
                                </template>

                                <div class="mt-4 flex justify-end gap-2">
                                    <button type="button" @click="cerrarModalClientes()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Guardar Proyecto</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
@stop
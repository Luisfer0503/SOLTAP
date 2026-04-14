@extends('principal')

@section('contenido')

<main class="flex-1 p-8">
    <h2 class="text-2xl font-bold mb-6">Clientes</h2>

    <div x-data="clientesModule()" x-cloak>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <input x-model="searchTerm" type="text" placeholder="Buscar por ID o nombre..." class="rounded-lg border px-3 py-2 w-80 uppercase" />
            </div>
            <div class="text-sm text-gray-500">Total: <span x-text="clients.length"></span></div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 text-left">ID</th>
                        <th class="p-2 text-left">Cliente</th>
                        <th class="p-2 text-left">Empresa</th>
                        <th class="p-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="cliente in getFilteredClients()" :key="cliente.id">
                        <tr class="border-t">
                            <td class="p-2" x-text="cliente.id"></td>
                            <td class="p-2" x-text="cliente.nombre_completo"></td>
                            <td class="p-2" x-text="cliente.empresa_nombre"></td>
                            
                            <td class="p-2 flex gap-2">
                                <button @click.prevent="abrirModal(cliente.id, cliente)" class="px-3 py-1 bg-blue-600 text-white rounded">Agregar Proyecto</button>
                                <button @click.prevent="verProyectos(cliente.id, cliente)" class="px-3 py-1 bg-teal-600 text-white rounded">Ver Proyectos</button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="getFilteredClients().length === 0">
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No se encontraron clientes</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="mostrarModal" class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center" @click.self="cerrarModal()" style="display:none;">
            <div class="bg-white rounded-lg w-11/12 max-w-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Agregar Proyecto a <span x-text="clienteSeleccionadoNombre"></span></h3>

                <template x-if="mensaje">
                    <div class="mb-3 p-3 bg-green-100 text-green-700 rounded" x-text="mensaje"></div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600">Empresa</label>
                        <select x-model="empresaSeleccionada" @change="generarPrefijo()" class="w-full rounded border px-2 py-2">
                            <option value="">Seleccionar empresa...</option>
                            <template x-for="emp in empresas" :key="emp.empresa_id">
                                <option :value="emp.empresa_id" x-text="emp.nombre"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Google Maps</label>
                        <input type="text" x-model="mapsUrl" class="w-full rounded border px-2 py-2" />
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600">Estado</label>
                        <select x-model="estadoEntrega" @change="generarMapsEntrega()" class="w-full rounded border px-2 py-2">
                            <option value="">Seleccionar...</option>
                            <template x-for="est in estados" :key="est.estado_id">
                                <option :value="est.estado_id" x-text="est.nombre"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Municipio</label>
                        <input x-model="municipioEntrega" @input="generarMapsEntrega()" class="w-full rounded border px-2 py-2 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Calle</label>
                        <input x-model="calleEntrega" @input="generarMapsEntrega()" class="w-full rounded border px-2 py-2 uppercase">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs text-gray-600">Descripción</label>
                    <textarea x-model="descripcion" class="w-full rounded border px-2 py-2 uppercase" rows="3"></textarea>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600">Enfoque</label>
                        <select x-model="enfoqueSeleccionado" class="w-full rounded border px-2 py-2">
                            <option value="">Seleccionar enfoque...</option>
                            <template x-for="enf in enfoques" :key="enf.enfoque_id">
                                <option :value="enf.enfoque_id" x-text="enf.nombre"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs text-gray-600">Nombre del Proyecto</label>
                    <div class="flex gap-2 mt-1">
                        <input type="text" readonly x-model="prefijoProyecto" class="w-40 rounded border px-2 py-2 bg-gray-100">
                    <input type="text" x-model="partePersonalizada" @input="actualizarNombreCompleto()" placeholder="Parte personalizada..." class="flex-1 rounded border px-2 py-2 uppercase">
                    </div>
                    <input type="hidden" x-model="nombreProyectoCompleto">
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button @click="cerrarModal()" class="px-3 py-2 rounded bg-gray-200">Cancelar</button>
                    <button @click="guardarProyecto()" :disabled="cargandoGuardar" class="px-4 py-2 rounded bg-indigo-600 text-white disabled:opacity-50">
                        <span x-text="cargandoGuardar ? 'Guardando...' : 'Guardar Proyecto'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Ver Proyectos -->
        <div x-show="mostrarModalProyectos" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center" @click.self="cerrarModalProyectos()" style="display:none;">
            <div class="bg-white rounded-lg w-11/12 max-w-4xl p-6 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Proyectos de <span x-text="clienteSeleccionadoNombre"></span></h3>
                    <button @click="cerrarModalProyectos()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <div x-show="cargandoProyectos" class="text-center py-4">
                    <p class="text-gray-500">Cargando proyectos...</p>
                </div>

                <div x-show="!cargandoProyectos">
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 border text-left">ID</th>
                                <th class="p-2 border text-left">Nombre Proyecto</th>
                                <th class="p-2 border text-left">Estatus</th>
                                <th class="p-2 border text-left">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="proy in proyectosCliente" :key="proy.proyecto_id">
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2 border" x-text="proy.proyecto_id"></td>
                                    <td class="p-2 border font-medium" x-text="proy.nombre"></td>
                                    <td class="p-2 border">
                                        <span class="px-2 py-1 rounded text-xs" 
                                            :class="proy.estatus ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                            x-text="proy.estatus || 'Nuevo'"></span>
                                    </td>
                                    <td class="p-2 border text-gray-600" x-text="proy.descripcion || '-'"></td>
                                </tr>
                            </template>
                            <template x-if="proyectosCliente.length === 0">
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">Este cliente no tiene proyectos registrados.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="cerrarModalProyectos()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function clientesModule(){
    return {
        mostrarModal: false,
        mostrarModalProyectos: false,
        proyectosCliente: [],
        cargandoProyectos: false,
        clienteSeleccionadoId: null,
        clienteSeleccionadoNombre: '',
        clients: @json($clientes),
        searchTerm: '',
        empresas: @json($empresas),
        estados: @json($estados),
        enfoques: @json($enfoques),
        prefijoProyecto: '',
        partePersonalizada: '',
        nombreProyectoCompleto: '',
        estadoEntrega: '',
        municipioEntrega: '',
        calleEntrega: '',
        empresaSeleccionada: null,
        descripcion: '',
        enfoqueSeleccionado: null,
        mapsUrl: '',
        mensaje: '',
        cargandoGuardar: false,

        abrirModal(id, cliente){
            this.clienteSeleccionadoId = id;
            this.clienteSeleccionadoNombre = cliente.nombre_completo || '';
            this.mostrarModal = true;
            this.partePersonalizada = '';
            this.prefijoProyecto = '';
            this.nombreProyectoCompleto = '';
            // generar prefijo inicial usando datos del cliente (si vienen)
            this.generarPrefijoFromClient(cliente);
            // inicializar campos adicionales
            this.empresaSeleccionada = cliente.empresa_id || null;
            this.descripcion = '';
            this.enfoqueSeleccionado = cliente.enfoque_id || null;
            this.mapsUrl = cliente.maps || '';
            this.calleEntrega = cliente.calle || '';
            this.municipioEntrega = cliente.municipio || '';
            this.estadoEntrega = cliente.estado_id || '';
        },

        cerrarModal(){
            this.mostrarModal = false;
            this.mensaje = '';
            this.empresaSeleccionada = null;
            this.descripcion = '';
            this.enfoqueSeleccionado = null;
            this.mapsUrl = '';
        },

        cerrarModalProyectos(){
            this.mostrarModalProyectos = false;
            this.proyectosCliente = [];
        },

        generarPrefijoFromClient(cliente){
            try{
                const empresaId = cliente.empresa_id || null;
                let codigoEmpresa = '';
                if (empresaId) {
                    const emp = this.empresas.find(e => e.empresa_id == empresaId);
                    if (emp) {
                        const ename = (emp.nombre||'').toLowerCase();
                        if (ename.includes('casa tapier')) codigoEmpresa = 'CT';
                        else if (ename.includes('solferino')) codigoEmpresa = 'SH';
                        else codigoEmpresa = emp.nombre.substring(0,2).toUpperCase();
                    }
                }
                this.prefijoProyecto = `${codigoEmpresa}-`;
                this.nombreProyectoCompleto = this.prefijoProyecto + (this.partePersonalizada||'');
            }catch(e){console.error(e)}
        },

        getFilteredClients(){
            if(!this.searchTerm) return this.clients || [];
            const term = (this.searchTerm||'').toString().toLowerCase();
            return (this.clients||[]).filter(c=>{
                const idMatch = String(c.id||'').includes(term);
                const nombre = (c.nombre_completo||'').toString().toLowerCase();
                const nombreMatch = nombre.includes(term);
                return idMatch || nombreMatch;
            });
        },

        generarPrefijo(){
            let codigoEmpresa = '';
            if (this.empresaSeleccionada) {
                const emp = this.empresas.find(e => e.empresa_id == this.empresaSeleccionada);
                if (emp) {
                    const ename = (emp.nombre||'').toLowerCase();
                    if (ename.includes('casa tapier')) codigoEmpresa = 'CT';
                    else if (ename.includes('solferino')) {
                        codigoEmpresa = 'SH';
                        this.enfoqueSeleccionado = 3; // Auto-select Enfoque 3 for Solferino
                    }
                    else codigoEmpresa = emp.nombre.substring(0,2).toUpperCase();
                }
            }
            this.prefijoProyecto = `${codigoEmpresa}-`;
            // prefijo de proyecto a partir del cliente
            this.nombreProyectoCompleto = this.prefijoProyecto + (this.partePersonalizada||'');
            // generar URL de Maps si hay dirección de entrega
            if (this.estadoEntrega && this.municipioEntrega && this.calleEntrega) {
                const estadoObj = this.estados.find(e=>e.estado_id == this.estadoEntrega);
                const estadoNombre = estadoObj ? estadoObj.nombre : '';
                const q = `${this.calleEntrega}, ${this.municipioEntrega}, ${estadoNombre}`;
                this.mapsUrl = 'https://www.google.com/maps?q=' + encodeURIComponent(q);
            }
        },

        generarMapsEntrega(){
            if (this.estadoEntrega && this.municipioEntrega && this.calleEntrega) {
                const estadoObj = this.estados.find(e=>e.estado_id == this.estadoEntrega);
                const estadoNombre = estadoObj ? estadoObj.nombre : '';
                const q = `${this.calleEntrega}, ${this.municipioEntrega}, ${estadoNombre}`;
                this.mapsUrl = 'https://www.google.com/maps?q=' + encodeURIComponent(q);
            }
        },

        actualizarNombreCompleto(){
            this.nombreProyectoCompleto = this.prefijoProyecto + (this.partePersonalizada||'');
        },

        async guardarProyecto(){
            if (this.cargandoGuardar) return;
            if (!this.clienteSeleccionadoId) return;
            if (!this.nombreProyectoCompleto) { this.mensaje = 'Nombre de proyecto vacío'; return; }
            
            this.cargandoGuardar = true;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const fd = new FormData();
            fd.append('cliente_id', this.clienteSeleccionadoId);
            fd.append('nombre_proyecto', this.nombreProyectoCompleto);
            fd.append('cambiar_direccion', (this.calleEntrega && this.municipioEntrega && this.estadoEntrega) ? 'si' : 'no');
            if (this.calleEntrega && this.municipioEntrega && this.estadoEntrega) {
                fd.append('direccion_entrega', `${this.calleEntrega}, ${this.municipioEntrega}, ${this.estados.find(e=>e.estado_id==this.estadoEntrega).nombre}`);
            }
            if (this.empresaSeleccionada) fd.append('empresa_id', this.empresaSeleccionada);
            if (this.mapsUrl) fd.append('maps', this.mapsUrl);
            if (this.descripcion) fd.append('descripcion', this.descripcion);
            if (this.enfoqueSeleccionado) fd.append('enfoque_id', this.enfoqueSeleccionado);
            try{
                const res = await fetch('{{ route('guardarProyecto') }}', { method:'POST', headers:{'X-CSRF-TOKEN': token,'Accept':'application/json'}, body: fd });
                const json = await res.json();
                if (!res.ok) { this.mensaje = json.error || 'Error'; this.cargandoGuardar = false; return; }
                this.mensaje = json.mensaje || 'Proyecto creado';
                if(json.cliente_id) console.log('Cliente ID:', json.cliente_id);
                setTimeout(()=>{ this.cerrarModal(); this.cargandoGuardar = false; }, 900);
            }catch(err){ console.error(err); this.mensaje = 'Error de red'; this.cargandoGuardar = false; }
        },

        async verProyectos(id, cliente){
            this.clienteSeleccionadoId = id;
            this.clienteSeleccionadoNombre = cliente.nombre_completo || '';
            this.mostrarModalProyectos = true;
            this.cargandoProyectos = true;
            this.proyectosCliente = [];
            
            try {
                // Asumiendo que la ruta se define como /erp/clientes/{id}/proyectos o similar
                // Si no tienes la ruta nombrada en JS, usamos la URL directa basada en tu estructura
                const res = await fetch(`{{ url('crm/clientes') }}/${id}/proyectos`);
                if(res.ok){
                    this.proyectosCliente = await res.json();
                } else {
                    console.error('Error al cargar proyectos');
                }
            } catch(e){
                console.error(e);
            } finally {
                this.cargandoProyectos = false;
            }
        }
    }
}
</script>

@stop

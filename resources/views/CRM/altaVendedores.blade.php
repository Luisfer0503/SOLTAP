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
<script src="//unpkg.com/alpinejs" defer></script>

<div class="flex-1 overflow-y-auto p-8" x-data="vendedoresForm()">
    <div class="max-w-4xl mx-auto">
        
        @if(session('mensaje'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('mensaje') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p class="font-bold">Por favor corrige los siguientes errores:</p>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Alta de Vendedor</h3>
                <div>
                    <button @click="showVendedores = true" class="px-3 py-2 bg-white border rounded text-sm">Lista de Vendedores</button>
                </div>
            </div>

            <!-- Modal Lista de Vendedores -->
            <div x-show="showVendedores" x-cloak @keydown.escape.window="showVendedores = false">
                <div class="fixed inset-0 bg-black/40 z-40" @click="showVendedores = false"></div>
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg w-full max-w-3xl max-h-[80vh] overflow-y-auto shadow-lg">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h4 class="font-bold text-gray-800">Lista de Vendedores</h4>
                            <button @click="showVendedores = false" class="px-3 py-1 text-sm text-gray-600">Cerrar</button>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-xs text-gray-500 uppercase">
                                        <tr>
                                            <th class="px-3 py-2 text-left">ID</th>
                                            <th class="px-3 py-2 text-left">Nombre</th>
                                            <th class="px-3 py-2 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vendedores as $v)
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-3 py-2 align-top">{{ $v->vendedor_id }}</td>
                                            <td class="px-3 py-2 align-top">{{ $v->nombre }} {{ $v->apellido_paterno }} {{ $v->apellido_materno }}</td>
                                            <td class="px-3 py-2 align-top text-right">
                                                <button type="button" @click="editar({{ json_encode($v) }})" class="inline-block px-3 py-1 bg-blue-600 text-white rounded text-xs mr-2">Editar</button>
                                                <form action="{{ route('eliminarVendedor', $v->vendedor_id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este vendedor?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form :action="formAction" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf 
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Vendedor</label>
                            <div class="relative">
                                <i class="ph ph-hash absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="vendedor_id" value="{{ $sigue }}" readonly 
                                    class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-200 text-gray-600 cursor-not-allowed py-2.5">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                            <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Empresa</label>
                            <div class="space-y-2">
                                @forelse($empresas as $empresa)
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="empresa" value="{{ $empresa->empresa_id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" required>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $empresa->nombre }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-red-600">No hay empresas disponibles</p>
                                @endforelse
                            </div>
                            @error('empresa')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2 w-full">Fotografía</label>
                        
                        <div class="mb-4 w-40 h-40 rounded-full overflow-hidden border-4 border-gray-100 shadow-md bg-gray-50 flex items-center justify-center">
                            <img :src="fotoPreview" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/150?text=Sin+Foto'">
                        </div>

                        <div class="w-full">
                            <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-blue-400 focus:outline-none" id="drop-area">
                                <span class="flex items-center space-x-2">
                                    <i class="ph ph-upload-simple text-2xl text-gray-600"></i>
                                    <span class="font-medium text-gray-600">Seleccionar imagen</span>
                                </span>
                                <input type="file" name="foto" class="hidden" accept="image/*" @change="updatePreview($event)">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG. Máx 3MB.</p>
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" @click="cancelar()" class="px-4 py-2 bg-white border rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md">
                        <span x-text="isEditing ? 'Actualizar Vendedor' : 'Guardar Vendedor'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function vendedoresForm() {
        return {
            showVendedores: false,
            isEditing: false,
            formAction: '{{ route('guardarVendedor') }}',
            fotoPreview: 'https://via.placeholder.com/150?text=Sin+Foto',
            storageUrl: '{{ asset("storage") }}',
            
            editar(v) {
                this.isEditing = true;
                this.showVendedores = false;
                let url = '{{ route("actualizarVendedor", "PLACEHOLDER") }}';
                this.formAction = url.replace('PLACEHOLDER', v.vendedor_id);
                
                // Llenar campos
                document.querySelector('input[name="vendedor_id"]').value = v.vendedor_id;
                document.querySelector('input[name="nombre"]').value = v.nombre;
                document.querySelector('input[name="apellido_paterno"]').value = v.apellido_paterno;
                document.querySelector('input[name="apellido_materno"]').value = v.apellido_materno;
                
                // Seleccionar empresa
                const radio = document.querySelector(`input[name="empresa"][value="${v.empresa_id}"]`);
                if(radio) radio.checked = true;
                
                // Cargar imagen
                if (v.foto) {
                    this.fotoPreview = this.storageUrl + '/' + v.foto;
                } else {
                    this.fotoPreview = 'https://via.placeholder.com/150?text=Sin+Foto';
                }
            },
            
            cancelar() {
                this.isEditing = false;
                this.formAction = '{{ route('guardarVendedor') }}';
                document.querySelector('form').reset();
                this.fotoPreview = 'https://via.placeholder.com/150?text=Sin+Foto';
                document.querySelector('input[name="vendedor_id"]').value = '{{ $sigue }}';
            },
            
            updatePreview(event) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.fotoPreview = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        }
    }
</script>

@stop
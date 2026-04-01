@extends('principal')

@section('contenido')

<div class="flex-1 overflow-y-auto p-8">
    <div class="max-w-4xl mx-auto">
        
        @if(session('mensaje'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('mensaje') }}</p>
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
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Editar Vendedor</h3>
            </div>

            <form action="{{ route('actualizarVendedor', $vendedor->vendedor_id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf 
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Vendedor</label>
                            <div class="relative">
                                <i class="ph ph-hash absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="vendedor_id" value="{{ $vendedor->vendedor_id }}" readonly 
                                    class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-200 text-gray-600 cursor-not-allowed py-2.5">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $vendedor->nombre) }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $vendedor->apellido_paterno) }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                            <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $vendedor->apellido_materno) }}" required 
                                class="w-full rounded-lg border-gray-300 border px-4 py-2 focus:ring-blue-500 transition">
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Empresa</label>
                            <div class="space-y-2">
                                @forelse($empresas as $empresa)
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="empresa" value="{{ $empresa->empresa_id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" @if(isset($vendedor->empresa_id) && $vendedor->empresa_id == $empresa->empresa_id) checked @endif required>
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
                            <img id="preview-img" src="{{ isset($vendedor->foto) && $vendedor->foto ? asset('storage/'.$vendedor->foto) : 'https://via.placeholder.com/150?text=Sin+Foto' }}" class="w-full h-full object-cover">
                        </div>

                        <div class="w-full">
                            <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-blue-400 focus:outline-none" id="drop-area">
                                <span class="flex items-center space-x-2">
                                    <i class="ph ph-upload-simple text-2xl text-gray-600"></i>
                                    <span class="font-medium text-gray-600">Seleccionar imagen</span>
                                </span>
                                <input type="file" name="foto" class="hidden" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG. Máx 3MB.</p>
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 bg-white border rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md">
                        Guardar Vendedor
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result; // Cambia la fuente de la imagen al archivo cargado
            }
            
            reader.readAsDataURL(input.files[0]); // Lee el archivo como URL de datos
        }
    }
</script>

@stop
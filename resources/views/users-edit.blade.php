@extends('principal')

@section('contenido')

<main class="flex-1 flex flex-col h-screen bg-gray-100">
    
    <header class="bg-white border-b px-8 py-4 shadow-sm shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-user-circle-gear text-blue-600 mr-2"></i> Editar Usuario
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-2xl mx-auto">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-md font-bold text-gray-800 flex items-center">
                        <i class="ph ph-pencil-simple mr-2 text-blue-500"></i> Modificar datos de: {{ $user->name }}
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-5">
                            
                            <div class="flex items-center space-x-4">
                                @if($user->foto)
                                    <img src="{{ asset('storage/' . $user->foto) }}" class="w-16 h-16 rounded-full border-2 border-gray-300 object-cover" alt="{{ $user->name }}">
                                @else
                                    <div class="w-16 h-16 rounded-full border-2 border-gray-300 bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xl">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <label for="photo" class="block text-sm font-bold text-gray-700 mb-1">Cambiar Foto de Perfil</label>
                                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-bold text-gray-700 mb-1">Rol</label>
                                <select name="role" id="role" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" {{ old('role', $user->role) == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="area" class="block text-sm font-bold text-gray-700 mb-1">Área</label>
                                <select name="area" id="area" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area', $user->area) == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="departamento" class="block text-sm font-bold text-gray-700 mb-1">Departamento</label>
                                <select name="departamento" id="departamento" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @foreach($departamentos as $depto)
                                        <option value="{{ $depto->id }}" {{ old('departamento', $user->departamento) == $depto->id ? 'selected' : '' }}>{{ $depto->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('departamento') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Nueva Contraseña</label>
                                <input type="password" name="password" id="password" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Dejar en blanco para no cambiar">
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('users.index') }}" class="py-2.5 px-5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold transition">
                                Cancelar
                            </a>
                            <button type="submit" class="py-2.5 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md transition flex justify-center items-center">
                                <i class="ph ph-floppy-disk mr-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

@endsection
@extends('principal')

@section('contenido')

<main class="flex-1 flex flex-col h-screen bg-gray-100">
    
    <header class="bg-white border-b px-8 py-4 shadow-sm shrink-0">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-users-three text-blue-600 mr-2"></i> Gestión de Usuarios
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-6xl mx-auto">
            
            @if(session('mensaje'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center shadow-sm">
                    <i class="ph ph-check-circle text-xl mr-3"></i> 
                    <div>
                        <p class="font-bold">¡Éxito!</p>
                        <p class="text-sm">{{ session('mensaje') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center shadow-sm">
                    <i class="ph ph-warning-circle text-xl mr-3"></i>
                    <div>
                        <p class="font-bold">¡Error!</p>
                        <p class="text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Columna Izquierda: Alta de Usuario -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-user-plus mr-2 text-blue-500"></i> Nuevo Usuario
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-5">
                                    <div>
                                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Juan Pérez">
                                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="role" class="block text-sm font-bold text-gray-700 mb-1">Rol</label>
                                        <select name="role" id="role" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="" disabled selected>Selecciona un rol</option>
                                            <option value="Administrador" {{ old('role') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                            <option value="Diseño y Ventas Tapier" {{ old('role') == 'Diseño y Ventas Tapier' ? 'selected' : '' }}>Diseño y Ventas Tapier</option>
                                            <option value="Coordinación Ventas Solferino" {{ old('role') == 'Coordinación Ventas Solferino' ? 'selected' : '' }}>Coordinación Ventas Solferino</option>
                                            <option value="Diseño y Ventas Solferino" {{ old('role') == 'Diseño y Ventas Solferino' ? 'selected' : '' }}>Diseño y Ventas Solferino</option>
                                        </select>
                                        @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="photo" class="block text-sm font-bold text-gray-700 mb-1">Foto de Perfil</label>
                                        <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="usuario@soltap.mx">
                                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Contraseña</label>
                                        <input type="password" name="password" id="password" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Mínimo 8 caracteres">
                                        <p class="text-xs text-gray-500 mt-1">Será visible solo en este momento. Cópiala y compártela de forma segura.</p>
                                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-md transition flex justify-center items-center">
                                        <i class="ph ph-plus-circle mr-2"></i> Crear Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Listado de Usuarios -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-md font-bold text-gray-800 flex items-center">
                                <i class="ph ph-users mr-2 text-gray-500"></i> Usuarios Registrados
                            </h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha de Alta</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        @if($user->foto)
                                            <img src="{{ asset('storage/' . $user->foto) }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover" alt="{{ $user->name }}">
                                        @else
                                            <div class="w-10 h-10 rounded-full border border-gray-200 bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->role }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center space-x-2">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Editar Usuario">
                                                <i class="ph ph-pencil-simple text-lg"></i>
                                            </a>
                                            @if(Auth::user()->id !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Eliminar Usuario">
                                                    <i class="ph ph-trash text-lg"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay usuarios registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
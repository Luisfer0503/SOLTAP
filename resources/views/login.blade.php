<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SOLTAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-200">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('archivos/logosshctmm.png') }}" alt="Logo Casa Tapier" class="h-24 w-auto object-contain">
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Bienvenido</h2>
            <p class="text-gray-500 text-sm mt-1">Ingresa a tu cuenta para continuar</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm font-medium text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-envelope-simple text-gray-400 text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                        placeholder="usuario@casatapier.com" value="{{ old('email') }}">
                </div>
                @error('email')
                    <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="block text-sm font-bold text-gray-700">Contraseña</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-bold">¿Primera vez o la olvidaste? Elígela aquí</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-lock-key text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                        placeholder="••••••••">
                </div>
                @error('password')
                    <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center mb-6">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="remember_me" class="ml-2 block text-sm text-gray-600">Recordarme en este equipo</label>
            </div>

            <!-- Botón Login -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition duration-200 flex justify-center items-center">
                <i class="ph ph-sign-in mr-2 text-lg"></i> Iniciar Sesión
            </button>
        </form>
    </div>

</body>
</html>
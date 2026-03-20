<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - SOLTAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-200">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4 border border-blue-100">
                <i class="ph ph-key text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Recuperar Contraseña</h2>
            <p class="text-gray-500 text-sm mt-2">Ingresa tu correo y te enviaremos un enlace para elegir una nueva contraseña.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm font-medium text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-6">
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-envelope-simple text-gray-400 text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" required autofocus class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm" placeholder="usuario@casatapier.com" value="{{ old('email') }}">
                </div>
                @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition duration-200 flex justify-center items-center">
                <i class="ph ph-paper-plane-tilt mr-2 text-lg"></i> Enviar Enlace
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-blue-600 transition font-medium flex items-center justify-center">
                <i class="ph ph-arrow-left mr-1"></i> Volver al Login
            </a>
        </div>
    </div>
</body>
</html>
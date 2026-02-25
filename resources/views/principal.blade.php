<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOLTAP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-slate-300 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" style="position: fixed; left: 0; top: 0; height: 100vh; z-index: 50;">
        <div class="p-6 flex items-center justify-between border-b border-slate-700">
            <div class="flex items-center flex-1">
                <img src="https://i.pravatar.cc/150?u=a042581f4e29026024d" class="w-10 h-10 rounded-full mr-3 border-2 border-blue-400">
                <div>
                    <h4 class="text-white font-semibold text-sm">Luis Coyotecatl</h4>
                    <p class="text-xs text-slate-400">Admin</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white md:hidden">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            
            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">CRM</p>
            <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-house text-lg mr-3"></i> Inicio
            </a>
            <a href="{{ route('altaProspectos') }}" class="{{ request()->routeIs('altaProspectos') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-user-plus text-lg mr-3"></i> Nuevo Prospecto
            </a>
             <a href="{{ route('altaVendedores') }}" class="{{ request()->routeIs('altaVendedores') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-user-plus text-lg mr-3"></i> Alta Vendedores
            </a>
            <a href="{{ route('asignacionVendedor') }}" class="{{ request()->routeIs('asignacionVendedor') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-handshake text-lg mr-3"></i> Asignación de Vendedor/Diseñador
            </a>
            <a href="{{ route('clientes') }}" class="{{ request()->routeIs('clientes') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-users text-lg mr-3"></i> Clientes
            </a>
            <a href="{{ route('reporteEstatus') }}" class="{{ request()->routeIs('reporteEstatus') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-user-plus text-lg mr-3"></i> Reporte de Estatus
            </a>
            
            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">ERP</p>
            <a href="{{ route('gestionArticulos') }}" class="{{ request()->routeIs('gestionArticulos') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-package text-lg mr-3"></i> Alta de Artículos
            </a>
               <a href="{{ route('seguimientoProyectos') }}" class="{{ request()->routeIs('seguimientoProyectos') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-chart-line-up text-lg mr-3"></i> Seguimiento de Proyectos
            </a>

               <a href="{{ route('asignacionPrecios') }}" class="{{ request()->routeIs('asignacionPrecios') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-tag text-lg mr-3"></i> Asignación de Precios
            </a>

               <a href="{{ route('logistica') }}" class="{{ request()->routeIs('logistica') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-truck text-lg mr-3"></i> Logística
            </a>

            <a href="{{ route('altasCategorias') }}" class="{{ request()->routeIs('altasCategorias') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-truck text-lg mr-3"></i> Altas de Categorías
            </a>


        </nav>
    </aside>

    <!-- Overlay para mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 md:hidden" style="z-index: 40;"></div>

    <!-- Contenido Principal -->
    <div class="flex flex-col flex-1" :class="sidebarOpen && window.innerWidth >= 768 ? 'md:ml-64' : ''">
        <!-- Header con botón hamburguesa -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-700 hover:text-gray-900 transition">
                <i class="ph ph-list text-2xl"></i>
            </button>
            <div class="text-right">
                <p class="text-sm text-gray-600">Bienvenido</p>
            </div>
        </header>

        <!-- Contenido -->
        <div class="flex-1 overflow-auto">
            @yield('contenido')
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
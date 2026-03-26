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
            @auth
            <div class="flex items-center flex-1">
                @if(Auth::user()->foto)
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-10 h-10 rounded-full mr-3 border-2 border-blue-400 object-cover">
                @else
                    <div class="w-10 h-10 rounded-full mr-3 border-2 border-blue-400 bg-slate-600 flex items-center justify-center">
                        <span class="text-white font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                @endif
                <div>
                    <h4 class="text-white font-semibold text-sm">{{ Auth::user()->name }}</h4>
                    @php

                                        $userRoleName = \Illuminate\Support\Facades\DB::table('roles')->where('id', Auth::user()->role)->value('nombre') ?? Auth::user()->role;
                    $role = strtoupper($userRoleName);
                    
                    $rolesProduccionCoords = ['SUP. CARPINTERÍA', 'SUP. CARPINTERIA', 'SUP. BARNIZ Y LIJADO', 'COORD. INSTALACIÓN/SUP. HERRERÍA', 'COORD. INSTALACIÓN/SUP. HERRERÍA'];
                    $rolesProduccionScan = ['PRODUCCIÓN', 'PRODUCCION', 'LOGÍSTICA', 'LOGISTICA', 'ALMACÉN', 'ALMACEN', 'COORD. INSTALACIÓN', 'COORD. INSTALACION', 'COORD. PRODUCCIÓN/COMPRAS', 'COORD. PRODUCCION/COMPRAS'];

                    $canAccessCRM = in_array($role, ['ADMIN', 'COORD. DV&MKT', 'COORD. DV SOLFERINO']);
                    
                    $canAccessERP = in_array($role, ['ADMIN', 'VENDEDOR/DISEÑADOR']);
                    $canAccessSeguimiento = $canAccessERP || in_array($role, ['DIRECCIÓN', 'DIRECCION']) || in_array($role, $rolesProduccionCoords) || in_array($role, $rolesProduccionScan) || in_array($role, ['COORD. LOGÍSTICA', 'COORD. LOGISTICA']) ;
                    $canAccessAsignacionPrecios = $canAccessERP || in_array($role, ['COORD. DV SOLFERINO', 'COORD. DV&MKT', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA']);
                    $canAccessCobranza = in_array($role, ['ADMIN', 'COORD. DV&MKT', 'COORD. DV SOLFERINO', 'ADMINISTRACIÓN', 'ADMINISTRACION', 'DIRECCIÓN', 'DIRECCION']);
                    $canAccessLogistica = in_array($role, ['ADMIN', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA']);
                    $canAccessEscaner = $canAccessERP || in_array($role, $rolesProduccionCoords) || in_array($role, $rolesProduccionScan);
                    
                    $canAccessHistorialProyectos = $canAccessERP || in_array($role, ['DIRECCIÓN', 'DIRECCION', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA', 'COORD. DV&MKT']);
                    $canAccessInicio = $canAccessCRM || $canAccessERP || $canAccessHistorialProyectos || $canAccessSeguimiento || $canAccessAsignacionPrecios || $canAccessCobranza || $canAccessLogistica || $canAccessEscaner;
                    $showCRMHeader = $canAccessCRM;
                    $showERPHeader = $canAccessERP || $canAccessHistorialProyectos || $canAccessSeguimiento || $canAccessAsignacionPrecios || $canAccessCobranza || $canAccessLogistica || $canAccessEscaner;
                @endphp
                    <p class="text-xs text-slate-400">{{ $userRoleName ?? 'Usuario' }}</p>
                </div>
            </div>
            @endauth
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white md:hidden">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
        <nav class="flex-1 overflow-y-auto py-4">
            
            @if($showCRMHeader)
            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">CRM</p>
            @endif
        
            <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-house text-lg mr-3"></i> Inicio
            </a>

            @if($canAccessCRM)
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
                <i class="ph ph-user-plus text-lg mr-3"></i> Reporte de Prospectos
            </a>
            @endif
            
            @if($showERPHeader)
            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">ERP</p>
            @endif
            
            @if($canAccessHistorialProyectos)
            <a href="{{ route('reporteEstatusProyecto') }}" class="{{ request()->routeIs('reporteEstatusProyecto') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-clock-counter-clockwise text-lg mr-3"></i> Historial Proyectos
            </a>
            @endif
            
            @if($canAccessERP)
            <a href="{{ route('gestionArticulos') }}" class="{{ request()->routeIs('gestionArticulos') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-package text-lg mr-3"></i> Alta de Artículos
            </a>

            <a href="{{ route('altaEstatus') }}" class="{{ request()->routeIs('altaEstatus') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-package text-lg mr-3"></i> Alta de Estatus
            </a>
            @endif
            
            @if($canAccessSeguimiento)
               <a href="{{ route('seguimientoProyectos') }}" class="{{ request()->routeIs('seguimientoProyectos') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-chart-line-up text-lg mr-3"></i> Seguimiento de Proyectos
            </a>
            @endif

            @if($canAccessAsignacionPrecios)
               <a href="{{ route('asignacionPrecios') }}" class="{{ request()->routeIs('asignacionPrecios') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-tag text-lg mr-3"></i> Asignación de Precios
            </a>
            @endif
            
            @if($canAccessCobranza)
            <a href="{{ route('cobranza') }}" class="{{ request()->routeIs('cobranza') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-tag text-lg mr-3"></i> Cobranza
            </a>
            @endif

            @if($canAccessLogistica)
               <a href="{{ route('logistica') }}" class="{{ request()->routeIs('logistica') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-truck text-lg mr-3"></i> Logística
            </a>
            @endif
            
            @if($canAccessEscaner)
            <a href="{{ route('escanerProduccion') }}" class="{{ request()->routeIs('escanerProduccion') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-qr-code text-lg mr-3"></i> Escáner Producción
            </a>
            @endif
            
            @if(in_array($role, ['ADMIN']))
            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Administración</p>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.index') ? 'flex items-center px-6 py-3 bg-blue-600 text-white border-l-4 border-blue-400' : 'flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition' }}">
                <i class="ph ph-users-three text-lg mr-3"></i> Gestión de Usuarios
            </a>
            @endif

            <p class="px-6 text-xs font-bold text-slate-500 uppercase tracking-wider mt-6 mb-2">Cuenta</p>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-6 py-3 hover:bg-slate-700 hover:text-white transition">
                <i class="ph ph-sign-out text-lg mr-3 text-red-400"></i> Cerrar Sesión
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
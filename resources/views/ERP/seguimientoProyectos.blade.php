@extends('principal')

@section('contenido')

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">

    <header class="bg-white border-b px-8 py-5 shadow-sm z-10">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="ph ph-kanban text-blue-600 mr-3"></i> Seguimiento de Proyectos
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestiona tu tubería de ventas y prioridades.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('altaProspectos') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm text-sm font-medium flex items-center">
                    <i class="ph ph-user-plus mr-2"></i> Nuevo Prospecto
                </a>
            </div>
        </div>
    </header>

    <div class="px-8 pt-6 pb-2">
        
        @if($urgentes > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm flex items-center justify-between animate-pulse">
            <div class="flex items-center">
                <div class="bg-red-100 p-2 rounded-full mr-4">
                    <i class="ph ph-alarm text-2xl text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-800">¡Atención requerida!</h4>
                    <p class="text-sm text-red-700">Tienes <span class="font-bold text-lg">{{ $urgentes }}</span> cotización(es) que debe(n) entregarse mañana.</p>
                </div>
            </div>
            <button class="text-sm font-bold text-red-700 underline hover:text-red-900">Ver Urgentes</button>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                    <i class="ph ph-folder-open text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Activos</p>
                    <p class="text-xl font-bold text-gray-800">{{ count($proyectos->where('estatus', '!=', 'Cerrado / Ganado')) }}</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-3">
                    <i class="ph ph-clock-countdown text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Pendientes Cotizar</p>
                    <p class="text-xl font-bold text-gray-800">{{ count($proyectos->where('estatus', 'Cotización Pendiente')) }}</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-3">
                    <i class="ph ph-currency-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Ventas Mes</p>
                    <p class="text-xl font-bold text-gray-800">$350k</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-8 pb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-4 justify-between items-center">
                <div class="relative w-full md:w-64">
                    <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="text" placeholder="Buscar proyecto o cliente..." class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 border focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <div class="flex gap-2">
                    <select class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 text-gray-600">
                        <option>Todos los Estatus</option>
                        <option>Cotización Pendiente</option>
                        <option>En Proceso</option>
                    </select>
                    <select class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 text-gray-600">
                        <option>Este Mes</option>
                        <option>Próximos 7 días</option>
                    </select>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Proyecto / Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Avance</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Limite Cot.</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($proyectos as $p)
                    
                    {{-- Lógica visual para la fila --}}
                    @php
                        $fecha = \Carbon\Carbon::parse($p->fecha_limite);
                        $esUrgente = $fecha->isSameDay(\Carbon\Carbon::now()->addDay()) && $p->estatus !== 'Cerrado / Ganado';
                        $filaClass = $esUrgente ? 'bg-red-50' : 'hover:bg-gray-50';
                    @endphp

                    <tr class="{{ $filaClass }} transition">
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                    {{ substr($p->nombre, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $p->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->cliente }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap align-middle">
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1 max-w-[100px]">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $p->progreso }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1 block">{{ $p->progreso }}% Completado</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($esUrgente)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 animate-pulse">
                                    <i class="ph ph-warning mr-1"></i> Mañana
                                </span>
                            @else
                                <div class="text-sm text-gray-900">{{ $fecha->format('d M, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $fecha->diffForHumans() }}</div>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($p->estatus == 'Cotización Pendiente')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 border border-orange-200">
                                    Por Cotizar
                                </span>
                            @elseif($p->estatus == 'En Proceso')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    En Diseño
                                </span>
                            @elseif($p->estatus == 'Cerrado / Ganado')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    <i class="ph ph-check mr-1"></i> Ganado
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $p->estatus }}
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                
                                @if($p->estatus == 'Cotización Pendiente')
                                    <a href="#" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-md shadow-sm text-xs flex items-center">
                                        <i class="ph ph-calculator mr-1"></i> Cotizar
                                    </a>
                                @endif
                                
                                <a href="{{ route('detalleProyecto', $p->id) }}" class="text-gray-400 hover:text-gray-600 p-1 border border-gray-200 rounded bg-white inline-flex items-center justify-center" title="Ver Detalles">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>
                                <button class="text-gray-400 hover:text-blue-600 p-1 border border-gray-200 rounded bg-white" title="Editar">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div class="text-sm text-gray-700">
                    Mostrando <span class="font-medium">1</span> a <span class="font-medium">4</span> de <span class="font-medium">12</span> proyectos
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Anterior</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50">Siguiente</button>
                </div>
            </div>

        </div>
    </div>

</main>

@endsection
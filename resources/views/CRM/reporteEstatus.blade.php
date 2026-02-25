@extends('principal')

@section('contenido')


    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-100">
        <header class="h-16 bg-white border-b flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="ph ph-chart-bar text-blue-600 mr-2"></i> Reporte de Estatus
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('altaProspectos') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg flex items-center space-x-2 transition">
                    <i class="ph ph-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto">

                @if(session('mensaje'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('mensaje') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Filtros -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <form action="{{ route('reporteEstatus') }}" method="GET" class="flex items-end gap-4">
                        <div class="flex-1 max-w-xs">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Estatus</label>
                            <select name="estatus_id" class="w-full rounded-lg border-gray-300 bg-gray-50 border px-3 py-2">
                                <option value="">Todos los estatus</option>
                                @foreach($todosEstatus as $est)
                                    <option value="{{ $est->estatus_id }}" {{ $estatusId == $est->estatus_id ? 'selected' : '' }}>
                                        {{ $est->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Filtrar
                        </button>
                        @if($estatusId)
                            <a href="{{ route('reporteEstatus') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tabla -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                                <tr>
                                    <th class="px-6 py-3">Proyecto</th>
                                    <th class="px-6 py-3">Prospecto</th>
                                    <th class="px-6 py-3">Fecha Registro</th>
                                    <th class="px-6 py-3">Fecha Cliente</th>
                                    <th class="px-6 py-3">Fecha Venta No Concluida</th>
                                    <th class="px-6 py-3 text-center">Estatus Actual</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($resultados as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-medium text-gray-900">{{ $row->proyecto ?: 'Sin Proyecto' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $row->prospecto }}</td>
                                        <td class="px-6 py-3 text-gray-500">{{ \Carbon\Carbon::parse($row->fecha_registro)->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-3 text-green-600 font-medium">
                                            {{ $row->estatus === 'Cliente' ? \Carbon\Carbon::parse($row->fecha_actualizacion)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-3 text-red-600 font-medium">
                                            {{ $row->estatus === 'Venta no concluida' ? \Carbon\Carbon::parse($row->fecha_actualizacion)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="p-1 align-middle" style="height: 1px;">
                                            <div class="w-full h-full min-h-[40px] flex items-center justify-center font-bold text-white text-xs uppercase tracking-wide rounded shadow-sm
                                                {{ $row->estatus === 'Cliente' ? 'bg-green-600' : 
                                                  ($row->estatus === 'Venta no concluida' ? 'bg-red-600' : 
                                                  ($row->estatus === 'Prospecto' ? 'bg-blue-600' : 'bg-orange-500')) }}">
                                                {{ $row->estatus }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @if($row->estatus !== 'Venta no concluida' && $row->estatus !== 'Cliente')
                                                <form action="{{ route('cambiarEstatusVentaNoConcluida', $row->prospecto_id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de marcar este prospecto como Venta No Concluida?');">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline transition">
                                                        Marcar No Concluida
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No se encontraron registros.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

@stop
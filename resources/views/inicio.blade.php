@extends('principal')

@section('contenido')
    <main class="flex-1 flex flex-col">

        <div class="flex-1 overflow-y-auto p-8 relative">
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <p class="font-bold"><i class="ph ph-warning-circle mr-2"></i> {{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-xl p-6 mb-8 border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-bold text-gray-800">¡Hola de nuevo, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-gray-500 mt-1">Bienvenido al panel de control de <span class="text-blue-600 font-semibold">CASA TAPIER</span></p>
                </div>
                
                <div class="h-16 px-6 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <div class="flex items-center text-gray-500 font-bold tracking-widest uppercase">
                        <img src="{{asset('archivos/logoall.png')}}" alt="Logo Casa Tapier" class="h-28 w-auto mr-3 object-contain">
                    </div>
                </div>
            </div>

            <!-- Filtros Superiores -->
            <div class="flex justify-end mb-6">
                <div class="inline-flex bg-white rounded-lg shadow-sm border border-gray-200 p-1">
                    <a href="?filter=week" class="px-4 py-2 text-sm font-medium rounded-md {{ $filter == 'week' ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Semana</a>
                    <a href="?filter=month" class="px-4 py-2 text-sm font-medium rounded-md {{ $filter == 'month' ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Mes</a>
                    <a href="?filter=year" class="px-4 py-2 text-sm font-medium rounded-md {{ $filter == 'year' ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">Año</a>
                </div>
            </div>

            <!-- Tres Columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Columna Izquierda -->
                <div class="flex flex-col gap-6">
                    <!-- Gráfica lineal de Ventas Reales -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ventas Reales</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <!-- Gráfica de barras por Enfoques -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Ventas por Enfoque</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="enfoquesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Columna Central -->
                <div class="flex flex-col gap-6">
                    <!-- Gráfica de categorías -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Rendimiento por Categoría</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                    <!-- Espacio en blanco -->
                    <div class="bg-transparent p-6 rounded-xl border border-dashed border-gray-300 flex-1 flex items-center justify-center min-h-[250px]">
                        <span class="text-gray-400 text-sm">Espacio Disponible</span>
                    </div>
                </div>

                <!-- Columna Derecha: Métricas Clave -->
                <div class="flex flex-col gap-6">
                    <!-- Ventas Totales -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mr-4 shrink-0">
                            <i class="ph ph-currency-dollar"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Ventas Totales</p>
                            <p class="text-2xl font-bold text-gray-800">${{ number_format($totalSales, 2) }}</p>
                        </div>
                    </div>
                    <!-- Tasa de Conversión -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mr-4 shrink-0">
                            <i class="ph ph-trend-up"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Conversión de Leads</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($conversionRate, 2) }}%</p>
                        </div>
                    </div>
                    <!-- Valor Promedio de Remisiones -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mr-4 shrink-0">
                            <i class="ph ph-receipt"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">Promedio de Remisiones</p>
                            <p class="text-2xl font-bold text-gray-800">${{ number_format($avgRemision, 2) }}</p>
                        </div>
                    </div>
                    <!-- % de Anticipo Cobrado -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                        <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mr-4 shrink-0">
                            <i class="ph ph-wallet"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold mb-1">% Anticipo Cobrado</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($anticipoPercentage, 2) }}%</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesDates) !!},
                datasets: [{
                    label: 'Ventas Reales',
                    data: {!! json_encode($salesTotals) !!},
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Enfoques Chart
        const enfoquesCtx = document.getElementById('enfoquesChart').getContext('2d');
        new Chart(enfoquesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($enfoquesLabels) !!},
                datasets: [{
                    label: 'Ventas',
                    data: {!! json_encode($enfoquesTotals) !!},
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'],
                    borderRadius: 4,
                    barThickness: 30,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Categories Chart
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        new Chart(categoriesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($catLabels) !!},
                datasets: [{
                    label: 'Ventas',
                    data: {!! json_encode($catTotals) !!},
                    backgroundColor: '#6366F1',
                    borderRadius: 4,
                    barThickness: 20,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
@stop
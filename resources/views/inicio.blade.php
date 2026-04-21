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
                    <h2 class="text-2xl font-bold text-gray-800">¡Hola de nuevo, Luis! 👋</h2>
                    <p class="text-gray-500 mt-1">Bienvenido al panel de control de <span class="text-blue-600 font-semibold">CASA TAPIER</span></p>
                </div>
                
                <div class="h-16 px-6 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center">
                    <div class="flex items-center text-gray-500 font-bold tracking-widest uppercase">
                        <img src="{{asset('archivos/logoall.png')}}" alt="Logo Casa Tapier" class="h-28 w-auto mr-3 object-contain">
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Customer Service Representative</h1>
                    <div class="flex items-center mt-1 text-gray-500 text-sm">
                        <i class="ph ph-map-pin mr-1"></i> New York, USA
                        <span class="mx-2 text-blue-500 hover:underline cursor-pointer">Preview this post</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-white border rounded-full text-xs font-bold text-green-500 flex items-center shadow-sm">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Active
                    </span>
                    <button class="p-2 bg-white border rounded-full text-gray-400 hover:text-gray-600 shadow-sm"><i class="ph ph-pencil-simple"></i></button>
                    <button class="p-2 bg-white border rounded-full text-gray-400 hover:text-gray-600 shadow-sm"><i class="ph ph-trash"></i></button>
                </div>
            </div>

            <div class="border-b mb-8">
                <nav class="flex space-x-8">
                    <a href="#" class="border-b-2 border-blue-500 pb-4 text-blue-600 font-semibold text-sm uppercase tracking-wide">Summary</a>
                    <a href="#" class="pb-4 text-gray-500 hover:text-gray-700 font-semibold text-sm uppercase tracking-wide">Applicants</a>
                    <a href="#" class="pb-4 text-gray-500 hover:text-gray-700 font-semibold text-sm uppercase tracking-wide">Job Board</a>
                    <a href="#" class="pb-4 text-gray-500 hover:text-gray-700 font-semibold text-sm uppercase tracking-wide">Interviews</a>
                </nav>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 flex flex-col justify-between space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-center relative">
                        <div class="w-32 h-32 rounded-full border-4 border-emerald-100 border-l-emerald-500 flex items-center justify-center flex-col transform -rotate-45">
                             <div class="transform rotate-45 text-center">
                                 <span class="text-3xl font-bold text-gray-700 block">3,154</span>
                                 <span class="text-xs text-gray-400 uppercase font-semibold">Applicants</span>
                             </div>
                        </div>
                    </div>
                     <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full border-4 border-blue-100 border-l-blue-500 flex items-center justify-center flex-col transform -rotate-45">
                            <div class="transform rotate-45 text-center">
                                <span class="text-3xl font-bold text-gray-700 block">1,546</span>
                                <span class="text-xs text-gray-400 uppercase font-semibold">Interviews</span>
                            </div>
                       </div>
                    </div>
                </div>

             

                    <canvas id="applicantsChart" height="200"></canvas>
                    <div class="text-center mt-4">
                        <span class="px-4 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-500 uppercase">Applicants / Day</span>
                    </div>
                </div>
            </div>

      

        </div>
    </main>

    <script>
        const ctx = document.getElementById('applicantsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['S', 'M', 'T', 'W', 'T', 'F', 'S', 'S', 'M', 'T', 'W', 'T', 'F'],
                datasets: [{
                    label: 'Applicants',
                    data: [120, 200, 300, 382, 200, 250, 100, 130, 280, 350, 220, 180, 90],
                    backgroundColor: '#2CB1BC', 
                    borderRadius: 4,
                    barThickness: 15,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: true, beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
@stop
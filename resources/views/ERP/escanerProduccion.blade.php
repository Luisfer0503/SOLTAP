@extends('principal')

@section('contenido')
<!-- Incluimos la librería HTML5-QRCode para usar la cámara -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="//unpkg.com/alpinejs" defer></script>

<main class="flex-1 flex flex-col h-screen bg-gray-50" x-data="scannerApp()">
    <header class="bg-white border-b px-8 py-4 shadow-sm z-20">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="ph ph-qr-code text-indigo-600 mr-2"></i> Escáner de Orden de Producción
        </h2>
    </header>

    <div class="flex-1 overflow-y-auto p-8 flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200 w-full max-w-lg text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Escanee el código QR</h3>
            <p class="text-sm text-gray-500 mb-6">Alinee el código QR de la hoja de producción dentro del recuadro usando la cámara de su dispositivo.</p>

            <!-- Contenedor donde se mostrará la cámara -->
            <div id="reader" class="mx-auto w-full max-w-sm overflow-hidden rounded-lg border-4 border-dashed border-indigo-300 min-h-[250px] bg-gray-100 flex items-center justify-center">
                <i class="ph ph-camera text-5xl text-gray-300" x-show="!isScanning"></i>
            </div>

            <div class="mt-8 space-x-3">
                <button x-show="!isScanning" @click="startScanner" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold shadow hover:bg-indigo-700 transition">
                    <i class="ph ph-camera mr-2"></i> Iniciar Cámara
                </button>
                <button x-show="isScanning" @click="stopScanner" class="px-6 py-3 bg-red-600 text-white rounded-lg font-bold shadow hover:bg-red-700 transition" x-cloak>
                    <i class="ph ph-stop mr-2"></i> Detener
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    function scannerApp() {
        return {
            isScanning: false,
            html5QrCode: null,
            init() {
                this.html5QrCode = new Html5Qrcode("reader");
            },
            startScanner() {
                this.isScanning = true;
                // Configuración: usar cámara trasera y definir área de escaneo
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                this.html5QrCode.start({ facingMode: "environment" }, config, (decodedText, decodedResult) => {
                    // Redirigimos si detectamos una URL válida de nuestra aplicación
                    if (decodedText.includes('seguimientoProyectos')) {
                        this.stopScanner();
                        window.location.href = decodedText;
                    } else if (decodedText.includes('vistaProduccionProyecto')) {
                        // Soporte de compatibilidad para hojas impresas con la versión anterior del QR
                        this.stopScanner();
                        const urlParts = decodedText.split('/');
                        const projectId = urlParts[urlParts.length - 1];
                        window.location.href = '{{ route("seguimientoProyectos") }}?proyecto_id=' + projectId;
                    } else {
                        alert("El código QR escaneado no pertenece a una orden de producción válida.");
                    }
                }).catch((err) => {
                    console.error(err);
                    alert("No se pudo acceder a la cámara. Asegúrese de otorgar permisos.");
                    this.isScanning = false;
                });
            },
            stopScanner() {
                if (this.html5QrCode && this.isScanning) {
                    this.html5QrCode.stop().then(() => {
                        this.isScanning = false;
                    }).catch(err => console.error(err));
                }
            }
        }
    }
</script>
@endsection
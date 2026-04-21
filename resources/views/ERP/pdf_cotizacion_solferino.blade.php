<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Raleway';
            src: url({{ public_path('fonts/Raleway-Regular.ttf') }}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Raleway';
            src: url({{ public_path('fonts/Raleway-Bold.ttf') }}) format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @page {
            size: letter;
            margin: 1cm;
        }
        body {
            font-family: 'Raleway', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        /* Encabezado */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        /* Datos del Cliente */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            width: 150px;
        }
        /* Tabla de Productos */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: transparent;
            border-bottom: 2px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table td {
            border-bottom: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        /* ESTILO DE DIMENSIONES TIPO REJILLA */
        .tabla-dimensiones {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .tabla-dimensiones td {
            border: none !important;
            padding: 2px 0 !important;
            font-size: 8.5px;
            vertical-align: middle !important;
        }
        .label-dim {
            font-weight: bold;
            width: 35%;
            text-align: left;
        }
        .value-dim {
            width: 45%;
            text-align: center;
        }
        .unit-dim {
            font-weight: bold;
            width: 20%;
            text-align: center;
        }

        /* Totales */
        .totals-container {
            float: right;
            width: 250px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px;
            border: 1px solid #fff;
        }
        .bg-gray { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td width="60%">
            <img src="{{ public_path('archivos/logosolferino.png') }}" style="width: 180px; margin-bottom: 5px;">
        </td>
        <td width="40%" align="right">
            <img src="{{ public_path('archivos/osolferino.png') }}" style="width: 100px; margin-bottom: 5px;">        </td>
        </tr>
</table>

<table class="info-table">
    <tr>
        <td class="label">Fecha:</td>
        <td>{{ $fecha ?? '14 de abril de 2026' }}</td>
    </tr>
    <tr>
        <td class="label">Nombre del cliente:</td>
        <td>{{ $proyecto['cliente_nombre'] }}</td>
    </tr>
    <tr>
        <td class="label">Teléfono:</td>
        <td>{{ $proyecto['telefono'] }}</td>
    </tr>
    <tr>
        <td class="label">Asesor de venta:</td>
        <td>{{ mb_strtoupper($proyecto['vendedor_nombre'] ?? 'N/A') }}</td>
    </tr>
    <tr>
        <td class="label">Dirección de entrega:</td>
        <td>{{ $proyecto['direccion'] }}</td>
    </tr>
</table>

<table class="items-table">
    <tbody>
        <tr>
            <th width="15%">Referencia</th>
            <th width="12%">Art</th>
            <th width="36%">Descripción</th>
            <th width="5%">Cant</th>
            <th width="10%">Precio Unit.</th>
            <th width="10%">Adicional Unit.</th>
            <th width="12%">Precio Total</th>
        </tr>
        @foreach($articulos as $item)
        <tr>
            <td align="center" style="vertical-align: middle;">
                @php
                    $imagePath = null;
                    if (!empty($item['imagen'])) {
                        $rutaRelativa = preg_match('/storage\/(.*)$/', $item['imagen'], $m) ? $m[1] : $item['imagen'];
                        $local = public_path('storage/' . $rutaRelativa);
                        if (file_exists($local)) { $imagePath = $local; }
                    }
                @endphp
                @if($imagePath)
                    <img src="{{ $imagePath }}" style="width:80px; height:80px; object-fit:contain; display:block; margin:0 auto;">
                @else
                    N/A
                @endif
            </td>
            <td class="bold" style="vertical-align: middle;">{{ $item['nombre'] }}</td>
            <td>
                {{ $item['descripcion'] }}
                
                <table class="tabla-dimensiones">
                    <tr>
                        <td class="label-dim">L/A:</td>
                        <td class="value-dim">{{ number_format($item['ancho'], 0) }}</td>
                        <td class="unit-dim">cm</td>
                    </tr>
                    <tr>
                        <td class="label-dim">ALT:</td>
                        <td class="value-dim">{{ number_format($item['alto'], 0) }}</td>
                        <td class="unit-dim">cm</td>
                    </tr>
                    <tr>
                        <td class="label-dim">PRO:</td>
                        <td class="value-dim">{{ number_format($item['profundo'], 0) }}</td>
                        <td class="unit-dim">cm</td>
                    </tr>
                </table>
            </td>
            <td align="center" style="vertical-align: middle;">{{ $item['cantidad'] }}</td>
            <td align="center" style="vertical-align: middle;">$ {{ number_format($item['precio_unitario'], 2) }}</td>
            <td align="center" style="vertical-align: middle;">$ {{ number_format($item['adicional_unitario'] ?? 0, 2) }}</td>
            <td align="center" class="bold" style="vertical-align: middle;">$ {{ number_format($item['cantidad'] * ($item['precio_unitario'] + ($item['adicional_unitario'] ?? 0)), 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width: 100%; margin-top: 20px;">
    <tbody>
        <tr>
            <td style="width: 60%; vertical-align: top; padding-right: 20px; border: none;">
                <p><b>Tiempo de entrega:</b> {{ $tiempo_entrega ?? 'De 10 a 12 semanas más tiempo de envío después del pago total.' }}</p>
                <p><b>Entrega:</b> {{ !empty($proyecto['condiciones_acceso']) ? $proyecto['condiciones_acceso'] : 'Sin condiciones especiales de acceso.' }}</p>
                 <p><b>S O L F E R I N O H O M E . C O M . M X</b></p>
                <p><b>Observaciones:</b> {{ $observaciones ?? 'Al aceptar esta cotización, está conforme con los términos y condiciones proporcionados por el asesor de venta.' }}</p>
            </td>
            <td style="width: 40%; vertical-align: top; border: none;">
                <div class="totals-container" style="float: none; width: 100%;">
                    <table class="totals-table">
                        <tr>
                            <td class="bg-gray bold">Subtotal:</td>
                            <td class="text-right">$ {{ number_format($totales['subtotal_articulos'], 2) }}</td>
                        </tr>
                        @if(isset($totales['envio']) && $totales['envio'] > 0)
                        <tr>
                            <td class="bg-gray bold">Envío:</td>
                            <td class="text-right">$ {{ number_format($totales['envio'], 2) }}</td>
                        </tr>
                        @endif
                        @if(isset($totales['instalacion']) && $totales['instalacion'] > 0)
                        <tr>
                            <td class="bg-gray bold">Instalación:</td>
                            <td class="text-right">$ {{ number_format($totales['instalacion'], 2) }}</td>
                        </tr>
                        @endif
                        @if(isset($totales['descuento']) && $totales['descuento'] > 0)
                        <tr>
                            <td class="bg-gray bold">Descuento:</td>
                            <td class="text-right">$ {{ number_format($totales['descuento'], 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="bg-gray bold" style="font-size: 11px;">TOTAL:</td>
                            <td class="text-right bold" style="font-size: 11px;">$ {{ number_format($totales['total'], 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<!-- SALTO DE PÁGINA PARA TÉRMINOS Y CONDICIONES -->
<div style="page-break-before: always;"></div>

<div style="font-family: 'Helvetica', 'Arial', sans-serif;">
    <div style="width: 100%; text-align: center; margin-top: 0px; margin-bottom: 10px;">
        <img src="{{ public_path('archivos/logosolferino.png') }}" style="width: 140px; margin-bottom: 5px;">
        <h2 style="font-size: 14px; font-weight: bold; color: #000; margin: 0; text-transform: uppercase;">Términos y Condiciones</h2>
    </div>
    
    <div style="width: 100%; margin-top: 10px;">
        @if(isset($terminos) && count($terminos) > 0)
            @foreach($terminos as $termino)
                <div style="margin-bottom: 8px;">
                    <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 2px; color: #000; text-transform: uppercase;">{{ $termino->nombre ?? '' }}</h3>
                    <div style="font-size: 7.5px; text-align: justify; line-height: 1.2; color: #333;">
                        {!! nl2br(e($termino->contenido ?? '')) !!}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

</body>
</html>
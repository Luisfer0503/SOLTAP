<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: letter;
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        /* Encabezado */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .brand-subtitle {
            font-size: 10px;
            letter-spacing: 4px;
            color: #666;
        }
        /* Datos del Cliente */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
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
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }
        .dims {
            font-size: 8px;
            margin-top: 5px;
            color: #555;
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
            border: 1px solid #ccc;
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
        <td width="50%">
            <div class="logo-text">SOLFERINO</div>
            <div class="brand-subtitle">HOGAR & ESTILO</div>
        </td>
        <td width="50%" align="right">
            <div style="font-size: 14px; font-weight: bold;">COTIZACIÓN</div>
        </td>
    </tr>
</table>

<table class="info-table">
    <tr>
        <td class="label">Fecha:</td>
        <td>{{ date('d/m/Y') }}</td>
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
        <td>{{ $proyecto['asesor'] ?? 'MARIA ANDREA DIAZ PACHECO' }}</td>
    </tr>
    <tr>
        <td class="label">Dirección de entrega:</td>
        <td>{{ $proyecto['direccion'] }}</td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th width="15%">Art</th>
            <th width="45%">Descripción</th>
            <th width="10%">Cant</th>
            <th width="15%">Precio Unit.</th>
            <th width="15%">Precio Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($articulos as $item)
        <tr>
            <td class="bold">{{ $item['nombre'] }}</td>
            <td>
                {{ $item['descripcion'] }}
                <div class="dims">
                    L/A: {{ number_format($item['ancho'], 0) }} cm | 
                    ALT: {{ number_format($item['alto'], 0) }} cm | 
                    PRO: {{ number_format($item['profundo'], 0) }} cm
                </div>
            </td>
            <td align="center">{{ $item['cantidad'] }}</td>
            <td align="right">$ {{ number_format($item['precio_unitario'], 2) }}</td>
            <td align="right" class="bold">$ {{ number_format($item['cantidad'] * $item['precio_unitario'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals-container">
    <table class="totals-table">
        <tr>
            <td class="bg-gray bold">Subtotal:</td>
            <td class="text-right">$ {{ number_format($totales['subtotal_articulos'], 2) }}</td>
        </tr>
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

<div style="clear: both; margin-top: 30px;">
    <p><b>Tiempo de entrega:</b> De 10 a 12 semanas más tiempo de envío después del pago total.</p>
    <p><b>Observaciones:</b> Al aceptar esta cotización, está conforme con los términos y condiciones.</p>
</div>

<footer>
    SOLFERINOHOME.COM.MX | Av. Ayuntamiento #68 Col. Manantiales San Pedro Cholula Pue.
</footer>

</body>
</html>
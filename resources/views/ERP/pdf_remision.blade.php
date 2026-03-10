<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Remisión #{{ str_pad($cotizacionId, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 1cm; 
        }
        
        body { 
            font-family: sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.3; 
        }
        
        /* Estructura */
        .w-full { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-xs { font-size: 9px; color: #666; }
        
        /* Encabezado */
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #2d3748; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .company-name { 
            font-size: 24px; 
            font-weight: bold; 
            color: #1a202c; 
            text-transform: uppercase; 
        }
        .doc-title { 
            font-size: 20px; 
            color: #4a5568; 
            font-weight: bold; 
        }
        
        /* Cajas de Información */
        .info-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-box { 
            background-color: #f7fafc; 
            border: 1px solid #e2e8f0; 
            border-radius: 5px; 
            padding: 10px; 
            vertical-align: top;
        }
        .info-title { 
            font-weight: bold; 
            color: #2d3748; 
            font-size: 11px; 
            border-bottom: 1px solid #cbd5e0; 
            padding-bottom: 4px; 
            margin-bottom: 6px; 
            text-transform: uppercase;
        }
        .info-row { margin-bottom: 3px; }
        .label { font-weight: bold; color: #718096; margin-right: 5px; }
        
        /* Tabla de Artículos */
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .items-table th { 
            background-color: #2d3748; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-size: 10px; 
            text-transform: uppercase; 
        }
        .items-table td { 
            border-bottom: 1px solid #e2e8f0; 
            padding: 8px; 
            vertical-align: middle; 
        }
        .items-table tr:nth-child(even) { background-color: #f8fafc; }
        
        /* Totales */
        .totals-table { 
            width: 300px; 
            margin-left: auto; 
            border-collapse: collapse; 
        }
        .totals-table td { padding: 5px 10px; }
        .totals-table .t-label { text-align: right; font-weight: bold; color: #4a5568; }
        .totals-table .t-amount { text-align: right; font-weight: bold; color: #2d3748; }
        .total-final td { 
            border-top: 2px solid #2d3748; 
            font-size: 14px; 
            background-color: #edf2f7; 
            padding: 10px; 
        }
        
        /* Footer */
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 9px; 
            color: #a0aec0; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 10px; 
        }

        /* Tabla Pagos */
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        .payments-table th { background-color: #e2e8f0; padding: 5px; text-align: left; }
        .payments-table td { border-bottom: 1px solid #edf2f7; padding: 5px; }
        .payments-container { margin-top: 20px; page-break-inside: avoid; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <table class="header-table">
        <tr>
            <td width="60%" valign="bottom">
                <div class="company-name">CASA TAPIER</div>
                <div class="text-xs">Soluciones en Mobiliario y Diseño</div>
            </td>
            <td width="40%" valign="bottom" class="text-right">
                <div class="doc-title">REMISIÓN</div>
                <div style="color: #718096;">
                    <strong>Folio:</strong> #{{ str_pad($cotizacionId, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Fecha:</strong> {{ date('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Información Cliente y Proyecto -->
    <table class="info-container" cellspacing="0" cellpadding="0">
        <tr>
            <td width="49%">
                <div class="info-box">
                    <div class="info-title">Información del Cliente</div>
                    <div class="info-row"><span class="label">Cliente:</span> {{ $proyecto['cliente_nombre'] }}</div>
                    <div class="info-row"><span class="label">Teléfono:</span> {{ $proyecto['telefono'] }}</div>
                    <div class="info-row"><span class="label">Correo:</span> {{ $proyecto['correo'] }}</div>
                    <div class="info-row"><span class="label">Dirección:</span> {{ $proyecto['direccion'] }}</div>
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%">
                <div class="info-box">
                    <div class="info-title">Detalles del Proyecto</div>
                    <div class="info-row"><span class="label">Proyecto:</span> {{ $proyecto['nombre_proyecto'] }}</div>
                    <div class="info-row"><span class="label">ID Proyecto:</span> #{{ $proyecto['proyecto_id'] }}</div>
                    <div class="info-row"><span class="label">Vendedor:</span> {{ $proyecto['vendedor_nombre'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Artículos (Sin Imagen) -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="15%">Diferenciador</th>
                <th width="40%">Descripción del Artículo</th>
                <th width="15%">Dimensiones</th>
                <th width="5%" class="text-center">Cant.</th>
                <th width="10%" class="text-right">Precio Unit.</th>
                <th width="10%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articulos as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold">{{ $item['id_articulo_produccion'] ?? 'N/A' }}</div>
                </td>
                <td>
                    <div class="font-bold" style="font-size: 12px;">{{ $item['nombre'] }}</div>
                    <div class="text-xs" style="margin-top: 2px;">{{ $item['descripcion'] }}</div>
                </td>
                <td>
                    <div style="font-size: 10px;">
                        <strong>Dim:</strong> {{ $item['alto'] }} x {{ $item['ancho'] }} x {{ $item['profundo'] }} m
                    </div>
                </td>
                <td class="text-center font-bold" style="font-size: 12px;">{{ $item['cantidad'] }}</td>
                <td class="text-right">${{ number_format($item['precio_unitario'], 2) }}</td>
                <td class="text-right font-bold">${{ number_format($item['cantidad'] * $item['precio_unitario'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <table class="totals-table">
        <tr>
            <td class="t-label">Subtotal Artículos:</td>
            <td class="t-amount">${{ number_format($totales['subtotal_articulos'], 2) }}</td>
        </tr>
        @if($totales['descuento'] > 0)
        <tr>
            <td class="t-label" style="color: #e53e3e;">Descuento:</td>
            <td class="t-amount" style="color: #e53e3e;">- ${{ number_format($totales['descuento'], 2) }}</td>
        </tr>
        @endif
        <tr>
            <td class="t-label">Envío:</td>
            <td class="t-amount">${{ number_format($totales['envio'], 2) }}</td>
        </tr>
        <tr>
            <td class="t-label">Subtotal:</td>
            <td class="t-amount">${{ number_format($totales['subtotal'], 2) }}</td>
        </tr>
        <tr>
            <td class="t-label">IVA:</td>
            <td class="t-amount">${{ number_format($totales['iva'], 2) }}</td>
        </tr>
        <tr class="total-final">
            <td class="t-label">TOTAL A PAGAR:</td>
            <td class="t-amount">${{ number_format($totales['total'], 2) }}</td>
        </tr>
    </table>

    <!-- Plan de Pagos -->
    @if(isset($pagos) && count($pagos) > 0)
    <div class="payments-container">
        <div style="font-weight: bold; border-bottom: 1px solid #2d3748; margin-bottom: 5px;">PLAN DE PAGOS</div>
        <table class="payments-table">
            <thead>
                <tr>
                    <th width="50%">Concepto</th>
                    <th width="20%" class="text-center">Porcentaje</th>
                    <th width="30%" class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                <tr>
                    <td>{{ $pago['nombre'] }}</td>
                    <td class="text-center">{{ $pago['porcentaje'] }}%</td>
                    <td class="text-right">${{ number_format($pago['monto'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Casa Tapier • Documento de Remisión</p>
    </div>

</body>
</html>
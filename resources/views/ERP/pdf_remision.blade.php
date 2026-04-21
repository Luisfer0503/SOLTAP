<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>

@page{
margin: 1cm 1cm 3cm 1cm;
}

body{
font-family: DejaVu Sans, Arial, sans-serif;
font-size:10px;
color:#000;
}

/* HEADER */

.header{
text-align:center;
margin-bottom:5px;
}

.header h1{
font-size:10px;
margin:0;
}

.logo{
width:180px;
margin:1cm 0 5px 0;
}

/* DATOS CLIENTE */

.datos{
width:100%;
border-collapse:collapse;
margin-top:5px;
border-top:1px solid #ccc;
}

.datos td{
border-bottom:1px solid #ccc;
padding:4px;
}

.datos b{
font-weight:bold;
}

/* MENSAJE */

.mensaje{
text-align:center;
font-size:10px;
margin:0.5cm 0;
}

/* TABLA PRODUCTOS */

.tabla{
width:100%;
border-collapse:collapse;
}

.tabla th{
border:1px solid #ccc;
background:#e6e6e6;
padding:5px;
font-size:9px;
}

.tabla td{
border:1px solid #ccc;
padding:5px;
font-size:9px;
vertical-align:top;
}

.tabla td table td {
border:none;
}

.img{
width:70px;
height:60px;
object-fit:contain;
}

/* BLOQUE INFERIOR */

.resumen{
width:100%;
border-collapse:collapse;
margin-top:5px;
}

.resumen td{
border:1px solid #ccc;
padding:5px;
font-size:10px;
}

.azul{
color:#1b3fbf;
font-weight:bold;
text-align:center;
}

.total{
font-weight:bold;
}

.footer{
text-align:center;
margin-top:15px;
font-size:10px;
}

/* FOOTER FIJO */

.page-footer{
position:fixed;
bottom:-2cm;
left:0;
right:0;
text-align:center;
font-size:10px;
}

</style>
</head>

<body>

@php
    $isSH = str_starts_with(strtoupper($proyecto['nombre_proyecto'] ?? ''), 'SH-');
    $tipoRem = $tipo_remision ?? 'solferino';
    
    if ($tipoRem === 'tapier') {
        $titulo = 'REMISIÓN INTERNA DE VENTA CASA TAPIER';
        $logo = 'logo.png';
    } elseif ($isSH) {
        $titulo = 'REMISIÓN DE VENTA SOLFERINO HOME';
        $logo = 'logosolferino.png';
    } else {
        $titulo = 'REMISIÓN DE VENTA CASA TAPIER';
        $logo = 'logo.png';
    }
@endphp

<div class="header">
    <h1>{{ $titulo }}</h1>
</div>

<table style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
    <tr>
        <!-- 5% Espacio (Columna Cant.) -->
        <td width="5%"></td>

        <!-- 55% Logo (Columna Desc.) -->
        <td width="55%" align="center" style="vertical-align: middle;">
            <img src="{{ public_path('archivos/' . $logo) }}" style="width:180px;">
        </td>

        <!-- 40% Datos (Columna Precio + Total) -->
        <td width="40%" style="vertical-align: middle;">
            <div style="width: 100%; border: 1px solid #ccc;">
                <!-- REMISION LABEL (NEGRO) -->
                <div style="background-color: #333; color: #fff; text-align: center; font-weight: bold; padding: 2px; font-size: 10px;">
                    NO. DE REMISIÓN DE VENTA
                </div>
                <!-- NUMERO REMISION (BLANCO) -->
                <div style="background-color: #fff; color: #000; text-align: center; font-weight: bold; padding: 5px; font-size: 12px;">
                    {{ $cotizacionId }}
                </div>
                <!-- NUMERO CLIENTE (NEGRO) -->
                <div style="background-color: #333; color: #fff; text-align: center; font-weight: bold; padding: 2px; font-size: 10px;">
                &nbsp;
                </div>
            </div>
        </td>
    </tr>
</table>


<table class="datos">

<tr>
<td width="60%" style="color:#1b3fbf;"><b>Nombre:</b> <span style="color:#1b3fbf;">{{ $proyecto['cliente_nombre'] }}</span></td>
<td width="40%" style="color:#1b3fbf;"><b>Fecha:</b> <span style="color:#1b3fbf;">{{ date('d/m/Y') }}</span></td>
</tr>
<tr>
    <td style="color:#1b3fbf;"><b>RFC:</b> <span style="color:#1b3fbf;">{{ $rfc ?? '' }}</span></td>
    <td style="color:#1b3fbf;"><b>Correo:</b> <span style="color:#1b3fbf;">{{ $proyecto['correo'] }}</span></td>
</tr>

<tr>
<td colspan="2" style="color:#1b3fbf;"><b>Teléfono:</b> <span style="color:#1b3fbf;">{{ $proyecto['telefono'] }}</span></td>
</tr>

<tr>
<td colspan="2" style="color:#1b3fbf;"><b>Dirección:</b> <span style="color:#1b3fbf;">{{ $proyecto['direccion'] }}</span></td>
</tr>

<tr>
    <td colspan="2" style="color:#1b3fbf;">
        @if(!empty($condiciones))
            <b>Condiciones:</b> {{ $condiciones }}
        @elseif(!empty($pagos) && count($pagos) > 0)
            <b>Condiciones:</b> A {{ count($pagos) }} pagos.
        @endif
    </td>
</tr>

</table>


<div class="mensaje">
A CONTINUACIÓN SE MUESTRA LA DESCRIPCIÓN DE LOS ARTÍCULOS ADQUIRIDOS.
</div>



<table class="tabla">

<thead>

<tr>
<th width="5%">Cant.</th>
<th width="55%">Descripción del artículo (Dimensiones en cm. ↔↕↗)</th>
<th width="20%">Precio Unitario</th>
<th width="20%">Total</th>
</tr>

</thead>

<tbody>

@foreach($articulos as $item)

<tr>

<td align="center">
{{ $item['cantidad'] }}
</td>

<td>

<b>{{ $item['nombre'] }}</b>

<br>

<table style="width: 100%; font-size: 8px; margin-top: 5px; margin-bottom: 5px; border-collapse: collapse;">
   <tr style="text-align: center;">
    <td style="padding: 2px; font-weight: bold;">L/A</td>
    <td style="padding: 2px;">{{ 0 + number_format((float)$item['ancho'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; font-weight: bold;">x</td>
    
    <td style="padding: 2px; font-weight: bold;">ALT</td>
    <td style="padding: 2px;">{{ 0 + number_format((float)$item['alto'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; font-weight: bold;">x</td>
    
    <td style="padding: 2px; font-weight: bold;">PRO</td>
    <td style="padding: 2px;">{{ 0 + number_format((float)$item['profundo'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; width: 10%;"></td>
    
    <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)$item['cubicaje'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf;">-</td>
    <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)$item['cubicaje'] * $item['cantidad'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf;">-</td>
    <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)$item['peso'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf;">-</td>
    <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)$item['peso'] * $item['cantidad'], 1, '.', '') }}</td>
</table>

<br>

{{ $item['descripcion'] }}

</td>

<td align="center" style="color:#000; vertical-align: middle;">
<b>$ {{ number_format($item['precio_unitario'],2) }}</b>
</td>

<td align="center" style="color:#000; vertical-align: middle;">
<b>$ {{ number_format($item['cantidad'] * $item['precio_unitario'],2) }}</b>
</td>

</tr>

@endforeach

</tbody>

</table>



<table class="resumen">

<tr>

<td width="60%">

<table style="width:100%;border-collapse:collapse;">

<tr>
<td class="azul" colspan="2" style="text-align:right;">{{ 0 + number_format((float)($totales['cubicaje'] ?? 0), 1, '.', '') }} <span style="padding-left: 1.25cm;">{{ 0 + number_format((float)($totales['peso'] ?? 0), 1, '.', '') }}</span></td>
</tr>

<tr>
<td colspan="2" style="height:90px;"></td>
</tr>

<tr>
<td style="text-align:center;font-weight:bold;">
{{ $totales['articulos'] ?? 0 }}
</td>

<td style="text-align:center;font-weight:bold;">
ARTÍCULOS COTIZADOS
</td>
</tr>

</table>

</td>


<td width="40%">

<table style="width:100%;border-collapse:collapse;">

<tr>
<td class="total">SUBTOTAL PRODUCTOS:</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['subtotal_articulos'],2) }}</td>
</tr>

@if(isset($totales['envio']) && $totales['envio'] > 0)
<tr>
<td class="total">ENVÍO:</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['envio'],2) }}</td>
</tr>
@endif

@if(isset($totales['instalacion']) && $totales['instalacion'] > 0)
<tr>
<td class="total">INSTALACIÓN:</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['instalacion'], 2) }}</td>
</tr>
@endif

@if(isset($totales['descuento']) && $totales['descuento'] > 0)
<tr>
<td class="total">DESCUENTO:</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['descuento'],2) }}</td>
</tr>
@endif

<tr>
<td class="total">SUBTOTAL:</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['subtotal'],2) }}</td>
</tr>

<tr>
<td class="total">IVA ({{ $totales['iva_porcentaje'] ?? 16 }}%)</td>
<td align="right" style="color:#1b3fbf;">$ {{ number_format($totales['iva'],2) }}</td>
</tr>

<tr>
<td class="total">TOTAL A PAGAR:</td>
<td align="right" style="color:#1b3fbf;"><b>$ {{ number_format($totales['total_remision'] ?? $totales['total'], 2) }}</b></td>
</tr>

</table>

</td>

</tr>

</table>

<div class="footer">
A continuación se firma el presente documento de conformidad por ambas partes, 
aceptando la "Opción de pago", así como las condiciones y garantías dadas a conocer:

</div>



<table style="width:100%; margin-top:2cm; border-collapse:collapse;">
    <tr>
        <td width="40%" align="center" style="padding-bottom: 5px;">
            <img src="{{ public_path('archivos/' . $logo) }}" style="width:160px;">
        </td>
        <td width="20%"></td>
        <td width="40%"></td>
    </tr>
    <tr>
        <td width="40%" align="center" style="border-top:1px solid #ccc; padding-top: 5px;">{{ ($tipoRem === 'solferino' && $isSH) ? 'SOLFERINO HOME' : 'CASA TAPIER S.A. DE C.V.' }}</td>
        <td width="20%"></td>
        <td width="40%" align="center" style="border-top:1px solid #ccc; padding-top: 5px;">Nombre Completo y Firma del Cliente</td>
    </tr>
</table>



<div class="page-footer">

Av. Ayuntamiento #68 Col. Manantiales San Pedro Cholula Pue. C.P. 72757  
Tel: (221) 652 6360 / ventastapier@gmail.com

</div>

</body>
</html>
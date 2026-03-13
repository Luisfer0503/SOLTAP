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
font-size:11px;
margin: 0;
}

/* HEADER */

.header{
text-align:center;
}

.header h1{
font-size:16px;
margin:0;
}

.header p{
margin:2px;
font-size:10px;
}

/* CLIENTE */

.info{
margin-top:10px;
}

.info table{
width:100%;
}

.info td{
padding:3px;
}

/* TABLA PRODUCTOS */

.tabla{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

.tabla th{
background-color: #e6e6e6;
border:1px solid #000;
padding:6px;
font-size:10px;
text-transform: uppercase;
}

.tabla td{
border:1px solid #000;
padding:6px;
}

thead{
display:table-header-group;
}

tr{
page-break-inside: avoid;
}

/* IMAGEN */

.img{
width:60px;
height:60px;
object-fit:contain;
}

/* TOTALES */

.totalesGeneral{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

.totalesGeneral td{
border:1px solid black;
}

.azul{
color:blue;
font-weight:bold;
text-align:center;
}

.total{
font-weight:bold;
}

.footer{
margin-top:20px;
font-size:10px;
text-align:center;
}

.page-footer {
    position: fixed;
    bottom: -2.5cm;
    left: 0px;
    right: 0px;
    height: 2.5cm;
    text-align: center;
    font-size: 10px;
}
</style>

</head>

<body>

<div class="page-footer">
    <p style="margin:2px;">
        Av. Ayuntamiento #68 Col. Manantiales San Pedro Cholula Pue. C.P. 72757
    </p>
    <p style="margin:2px;">
        Tel: (221) 652 6360 / ventastapier@gmail.com
    </p>
</div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
    <tr>
        <!-- Espacio para el logo -->
        <td style="width: 30%; vertical-align: top;">
             <img src="{{ public_path('archivos/logo.png') }}" alt="Logo" style="width: 150px;"> 
        </td>

        <!-- Información de la empresa -->
        <td style="width: 40%; text-align: center; vertical-align: top;">
            <h1 style="font-size:16px; margin:0;">REMISIÓN DE VENTA CASA TAPIER</h1>
        </td>

        <!-- Información de la remisión -->
        <td style="width: 30%; text-align: right; vertical-align: top; font-size: 11px;">
            <b>NO. DE REMISIÓN DE VENTA:</b> {{ $cotizacionId }}<br><br>
            <b>NO. CLIENTE:</b> {{ $proyecto['proyecto_id'] }}
        </td>
    </tr>
</table>

<div class="info">
<table>
<tr>
<td><b>Nombre:</b> {{ $proyecto['cliente_nombre'] }}</td>
<td><b>Fecha:</b> {{ date('d/m/Y') }}</td>
</tr>
<tr>
<td><b>Teléfono:</b> {{ $proyecto['telefono'] }}</td>
<td><b>Correo:</b> {{ $proyecto['correo'] }}</td>
</tr>
<tr>
<td colspan="2">
<b>Dirección:</b> {{ $proyecto['direccion'] }}
</td>
</tr>
</table>
</div>

<center>
<p style="margin-top:10px;font-size:10px;">
A CONTINUACIÓN SE MUESTRA LA DESCRIPCIÓN DE PRODUCTOS ADQUIRIDOS.</p>
</p>
</center>


<table class="tabla">

<thead>

<tr>

<th width="6%">Cant.</th>

<th width="70%">
Descripción del artículo. (Dimensiones en cm.  ↔↕↗)
</th>

<th width="12%">
Precio Unitario
</th>

<th width="12%">
Total
</th>

</tr>

</thead>

<tbody>

@php

$totalCubicaje = 0;
$totalPeso = 0;
$totalArticulos = 0;

@endphp

@foreach($articulos as $item)

@php

$cubicaje = (float)$item['cubicaje'] * (float)$item['cantidad'];
$peso = (float)$item['peso'] * (float)$item['cantidad'];

$totalCubicaje += $cubicaje;
$totalPeso += $peso;
$totalArticulos += $item['cantidad'];

@endphp

<tr>

<td align="center">
{{ $item['cantidad'] }}
</td>

<td>

<b>{{ $item['nombre'] }}</b>

<br>

<div style="font-size:9px;font-weight:bold;">

{{ $item['alto'] }} x {{ $item['ancho'] }} x {{ $item['profundo'] }} cm

&nbsp;&nbsp;&nbsp;

{{ $item['cubicaje'] }} - {{ number_format($cubicaje,2) }}

&nbsp;&nbsp;&nbsp;

{{ $item['peso'] }} - {{ number_format($peso,2) }}

</div>

{{ $item['descripcion'] }}

</td>

<td align="right">

$ {{ number_format($item['precio_unitario'],2) }}

</td>

<td align="right">

$ {{ number_format($item['cantidad'] * $item['precio_unitario'],2) }}

</td>

</tr>

@endforeach

</tbody>

</table>



<!-- TOTALES -->

<table class="totalesGeneral">

<tr>

<!-- COLUMNA IZQUIERDA -->

<td width="55%">

<table style="width:100%;border-collapse:collapse;">

<tr>

<td class="azul">

{{ number_format($totalCubicaje,1) }}

</td>

<td class="azul">

{{ number_format($totalPeso,0) }}

</td>

</tr>

<tr>

<td colspan="2" style="height:60px;"></td>

</tr>

<tr>

<td style="text-align:center;font-weight:bold;">
{{ $totalArticulos }}
</td>

<td style="font-weight:bold;">
ARTICULOS COTIZADOS
</td>

</tr>

</table>

</td>


<!-- COLUMNA DERECHA -->

<td width="45%">

<table style="width:100%;border-collapse:collapse;">

<tr>

<td class="total">
SUBTOTAL PRODUCTOS:
</td>

<td align="right">
$ {{ number_format($totales['subtotal_articulos'],2) }}
</td>

</tr>

@if(isset($totales['descuento']) && $totales['descuento'] > 0)
<tr>

<td class="total">
ENVIO:
</td>

<td align="right">
$ {{ number_format($totales['envio'],2) }}
</td>

</tr>

<tr>

<td class="total">
DESCUENTO
</td>

<td align="right">
$ {{ number_format($totales['descuento'],2) }}
</td>

</tr>

<tr>

<td class="total">
SUBTOTAL:
</td>

<td align="right">
$ {{ number_format($totales['subtotal'],2) }}
</td>

</tr>
@endif

<tr>

<td class="total">
IVA ({{ isset($totales['iva_porcentaje']) ? $totales['iva_porcentaje'] :0}}%)
</td>

<td align="right">
$ {{ number_format($totales['iva'],2) }}
</td>

</tr>

<tr>

<td class="total">
TOTAL A PAGAR:
</td>

<td align="right" class="total">
$ {{ number_format($totales['total'],2) }}
</td>

</tr>

</table>

</td>

</tr>

</table>



<div class="footer">

<p>
A continuación se firma el presente documento de conformidad por ambas partes, aceptando la "Opción de pago", así como las condiciones de compra y garantía dadas a conocer:</p>

<table style="width:100%; margin-top:50px;">
<tr>
<td style="text-align:center; width:45%;">
    <img src="{{ public_path('archivos/logo.png') }}" alt="Logo" style="width: 100px; margin-bottom: 5px;"><br>
----------------------------------------------------<br>
CASA TAPIER S.A. DE C.V.
</td>
<td style="width:10%;"></td>
<td style="text-align:center; width:45%;">
----------------------------------------------------<br>
Nombre completo y Firma del Cliente
</td>
</tr>
</table>

</div>

</body>
</html>
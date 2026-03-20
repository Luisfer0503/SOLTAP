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
border-top:1px solid #000;
}

.datos td{
border-bottom:1px solid #000;
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
border:1px solid #000;
background:#e6e6e6;
padding:5px;
font-size:9px;
}

.tabla td{
border:1px solid #000;
padding:5px;
font-size:9px;
vertical-align:top;
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
border:1px solid #000;
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

<div class="header">

<h1>HOJA DE COTIZACIÓN CASA TAPIER</h1>

<img src="{{ public_path('archivos/logo.png') }}" class="logo">

</div>


<table class="datos">

<tr>
<td width="50%"><b>Nombre:</b> {{ $proyecto['cliente_nombre'] }}</td>
<td width="50%"><b>Fecha:</b> {{ date('d/m/Y') }}</td>
</tr>

<tr>
<td><b>Teléfono:</b> {{ $proyecto['telefono'] }}</td>
<td><b>Correo:</b> {{ $proyecto['correo'] }}</td>
</tr>

<tr>
<td colspan="2"><b>Dirección:</b> {{ $proyecto['direccion'] }}</td>
</tr>

</table>


<div class="mensaje">
ESTIMADO CLIENTE, A CONTINUACIÓN LE PRESENTAMOS LA COTIZACIÓN DE LOS PRODUCTOS SOLICITADOS A NUESTRO EQUIPO DE DISEÑO ESPERANDO SU PRONTA RESPUESTA.
</div>



<table class="tabla">

<thead>

<tr>
<th width="5%">Cant.</th>
<th width="55%">Descripción del artículo (Dimensiones en cm. ↔↕↗)</th>
<th width="15%">Imagen</th>
<th width="12%">Precio Unitario</th>
<th width="13%">Total</th>
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

<div style="width:100%;">
<span style="float:left;">
{{ 0 + number_format((float)$item['alto'], 2, '.', '') }} x {{ 0 + number_format((float)$item['ancho'], 2, '.', '') }} x {{ 0 + number_format((float)$item['profundo'], 2, '.', '') }} 
</span>
<span style="float:right; color:#1b3fbf; font-weight:bold;">
{{ 0 + number_format((float)$item['cubicaje'], 2, '.', '') }} - {{ 0 + number_format((float)$item['cubicaje'] * $item['cantidad'], 2, '.', '') }} - {{ 0 + number_format((float)$item['peso'], 2, '.', '') }} - {{ 0 + number_format((float)$item['peso'] * $item['cantidad'], 2, '.', '') }}
</span>
</div>
<div style="clear:both;"></div>

<br>

{{ $item['descripcion'] }}

</td>

<td align="center">

@php

$imagePath=null;

if(!empty($item['imagen'])){
if(preg_match('/storage\/.*$/',$item['imagen'],$m)){
$local=public_path($m[0]);
if(file_exists($local)){
$imagePath=$local;
}
}
}

@endphp
@if($imagePath)
<img src="{{ $imagePath }}" class="img">
@endif

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



<table class="resumen">

<tr>

<td width="60%">

<table style="width:100%;border-collapse:collapse;">

<tr>
<td class="azul" colspan="2" style="text-align:right;">{{ number_format($totales['cubicaje'] ?? 0, 1) }} - {{ number_format($totales['peso'] ?? 0, 1) }}</td>
</tr>

<tr>
<td colspan="2" style="height:90px;"></td>
</tr>

<tr>
<td style="text-align:center;font-weight:bold;">
{{ $totales['articulos'] ?? 0 }}
</td>

<td style="font-weight:bold;">
ARTÍCULOS COTIZADOS
</td>
</tr>

</table>

</td>


<td width="40%">

<table style="width:100%;border-collapse:collapse;">

<tr>
<td class="total">SUBTOTAL PRODUCTOS:</td>
<td align="right">$ {{ number_format($totales['subtotal_articulos'],2) }}</td>
</tr>

<tr>
<td class="total">ENVÍO:</td>
<td align="right">$ {{ number_format($totales['envio'],2) }}</td>
</tr>

@if(isset($totales['descuento']) && $totales['descuento'] > 0)
<tr>
<td class="total">DESCUENTO:</td>
<td align="right">$ {{ number_format($totales['descuento'],2) }}</td>
</tr>
@endif

<tr>
<td class="total">SUBTOTAL:</td>
<td align="right">$ {{ number_format($totales['subtotal'],2) }}</td>
</tr>

<tr>
<td class="total">IVA ({{ $totales['iva_porcentaje'] ?? 16 }}%)</td>
<td align="right">$ {{ number_format($totales['iva'],2) }}</td>
</tr>

<tr>
<td class="total">TOTAL A PAGAR:</td>
<td align="right"><b>$ {{ number_format($totales['total'],2) }}</b></td>
</tr>

</table>

</td>

</tr>

</table>



<div class="footer">

AGRADECEMOS SU ATENCIÓN QUEDANDO A SU ENTERO SERVICIO.  
A CONTINUACIÓN DESCRIBIMOS LAS CONDICIONES DE COMPRA Y GARANTÍA DE NUESTROS PRODUCTOS.

</div>



<div class="page-footer">

Av. Ayuntamiento #68 Col. Manantiales San Pedro Cholula Pue. C.P. 72757  
Tel: (221) 652 6360 / ventastapier@gmail.com

</div>

</body>
</html>
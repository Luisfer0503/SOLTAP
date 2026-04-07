<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
@page{
    size: landscape;
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
    font-size:14px;
    margin:0;
}

/* DATOS */
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

/* TABLA */
.tabla{
    width:100%;
    border-collapse:collapse;
    margin-top: 15px;
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

/* EVITAR CORTES EN PDF */
table { page-break-inside:auto; }
tr { page-break-inside:avoid; }

/* FOOTER */
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
    $partesNombre = explode(' ', trim($proyecto['cliente_nombre']));
    $primerNombre = $partesNombre[0] ?? '';
    $primerApellido = $partesNombre[1] ?? '';
    $letraApellido = $primerApellido ? ' ' . mb_substr($primerApellido, 0, 1) : '';
    $nomenclatura = mb_strtoupper($primerNombre . $letraApellido) . '-' . $proyecto['proyecto_id'];
    $nombreAbreviado = $primerNombre . ($primerApellido ? ' ' . mb_substr($primerApellido, 0, 1) . '.' : '');

    $totalArticulos = 0;
    foreach($articulos as $item) {
        $totalArticulos += $item['cantidad'];
    }
@endphp

<!-- HEADER -->
<table style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
<tr>

<td width="20%" align="left" style="vertical-align: top;">
@if(isset($qrImage))
    <img src="data:image/png;base64,{{ $qrImage }}" style="width:90px; height:90px; border:1px solid #000;">
@endif
</td>

<td width="40%" align="center" style="vertical-align: top;">
    <img src="{{ public_path('archivos/logo.png') }}" style="width:180px;">
</td>
<td width="40%" style="vertical-align: top;">
            <div style="width: 100%; border: 1px solid #ccc;">
                <!-- REMISION LABEL (NEGRO) -->
                <div style="background-color: #333; color: #fff; text-align: center; font-weight: bold; padding: 2px; font-size: 10px;">
                  Asesor/Diseñador
                </div>
                <!-- NUMERO REMISION (BLANCO) -->
                <div style="background-color: #fff; color: #000; text-align: center; font-weight: bold; padding: 5px; font-size: 12px;">
                     {{ $proyecto['vendedor_nombre'] ?? 'N/A' }}
                </div>
                <!-- NUMERO CLIENTE (NEGRO) -->
                <div style="background-color: #333; color: #fff; text-align: center; font-weight: bold; padding: 2px; font-size: 10px;">
                &nbsp;
                </div>
            </div>
</tr>
</table>

<!-- DATOS -->
<table class="datos">
<tr>
<td width="60%" style="color:#1b3fbf;"><b>IDCLI:</b> {{ $proyecto['cliente_id'] ?? 'N/A' }}</td>
<td width="40%" style="color:#1b3fbf;"><b>Fecha de Venta:</b> {{ date('d/m/Y') }}</td>
</tr>

<tr>
<td width="60%" style="color:#1b3fbf;"><b>Nombre del Cliente:</b> <span style="color:#1b3fbf;">{{ $nombreAbreviado }} </span></td>
<td width="40%" style="color:#1b3fbf;"><b>Tiempo de Entrega:</b> <span style="color:#1b3fbf;">{{ $proyecto['tiempo_entrega'] ?? 'N/A' }} </span></td>
</tr>

<tr>
<td colspan="2" width="60%" style="color:#1b3fbf;"><b>Nombre del Proyecto:</b><span style="color:#1b3fbf;"> {{ $proyecto['nombre_proyecto'] }}</td>
</tr>

<tr>
<td colspan="2" width="60%" style="color:#1b3fbf;"><b>Ciudad y Estado:</b><span style="color:#1b3fbf;"> {{ $proyecto['municipio'] ?? 'N/A' }}, {{ $proyecto['estado'] ?? 'N/A' }} </span></td>
</tr>

<tr>
<td colspan="2" width="60%" style="color:#1b3fbf;"><b>Fecha recepción Producción:</b></td>
</tr>

<tr>
<td colspan="2" width="60%" style="color:#1b3fbf;"><b>Cantidad artículos:</b> <span style="color:#1b3fbf;">{{ $totalArticulos }} </span> </td>
</tr>
</table>

<!-- TABLA PRINCIPAL -->
<table class="tabla">

<thead>
<tr>
<th width="5%">Cant.</th>
<th width="50%">Descripción del artículo (Dimensiones cm)</th>
<th width="10%">Estándar</th>
<th width="10%">Personalizado</th>
<th width="25%">Imagen</th>
</tr>
</thead>

<tbody>
@foreach($articulos as $item)
<tr>

<!-- CANT -->
<td align="center">{{ $item['cantidad'] }}</td>

<!-- DESCRIPCIÓN -->
<td>

<b>{{ $item['nombre'] }}</b><br>

<span style="font-size:8px; color:#555;">
{{ $item['id_articulo_produccion'] ?? '' }}
</span>

<!-- MEDIDAS -->
<table style="width: 100%; border: 1px solid #ccc; font-size: 8px; margin-top: 5px; margin-bottom: 5px; border-collapse: collapse;">
   <tr style="text-align: center;">
    <td style="padding: 2px; border-right: 1px solid #ccc; font-weight: bold;">L/A</td>
    <td style="padding: 2px; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['ancho'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; border-right: 1px solid #ccc; font-weight: bold;">x</td>
    
    <td style="padding: 2px; border-right: 1px solid #ccc; font-weight: bold;">ALT</td>
    <td style="padding: 2px; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['alto'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; border-right: 1px solid #ccc; font-weight: bold;">x</td>
    
    <td style="padding: 2px; border-right: 1px solid #ccc; font-weight: bold;">PRO</td>
    <td style="padding: 2px; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['profundo'], 2, '.', '') }}</td>
    
    <td style="padding: 2px; border-right: 1px solid #ccc; width: 10%;"></td>
    
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['cubicaje'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['cubicaje'] * $item['cantidad'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)$item['peso'], 1, '.', '') }}</td>
    <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
    <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)$item['peso'] * $item['cantidad'], 1, '.', '') }}</td>
</tr>
</table>

<br>
{{ $item['descripcion'] }}

</td>

<!-- ESTANDAR -->
<td align="center" style="vertical-align: middle;">
@php
$esCatalogoSH = (strtoupper(trim($item['categoria_nombre'] ?? '')) === 'CATALOGO SH' || strtoupper(trim($item['categoria_nombre'] ?? '')) === 'CATÁLOGO SH');
@endphp
<div style="width:14px; height:14px; border:1px solid #000; margin:0 auto; line-height: 11px; text-align: center; font-weight: bold; font-size: 10px;">
{!! $esCatalogoSH ? 'X' : '&nbsp;' !!}
</div>
</td>

<!-- PERSONALIZADO -->
<td align="center" style="vertical-align: middle;">
<div style="width:14px; height:14px; border:1px solid #000; margin:0 auto; line-height: 11px; text-align: center; font-weight: bold; font-size: 10px;">
{!! !$esCatalogoSH ? 'X' : '&nbsp;' !!}
</div>
</td>

<!-- IMAGEN -->
<td align="center" style="vertical-align: middle;">
@php
$imagePath = null;
if (!empty($item['imagen'])) {
    // Extraer solo la ruta interna en caso de traer prefijos y generar la ruta absoluta
    $rutaRelativa = preg_match('/storage\/(.*)$/', $item['imagen'], $m) ? $m[1] : $item['imagen'];
    $local = public_path('storage/' . $rutaRelativa);
    if (file_exists($local)) {
        $imagePath = $local;
    }
}
@endphp

@if($imagePath)
<img src="{{ $imagePath }}" style="width:220px; height:220px; object-fit:contain; display: block; margin: 0 auto;">
@else
N/A
@endif

</td>

</tr>
@endforeach
</tbody>

</table>

</body>
</html>
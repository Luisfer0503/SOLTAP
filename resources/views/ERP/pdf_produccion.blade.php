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
.header{
text-align:center;
margin-bottom:5px;
}
.header h1{
font-size:14px;
margin:0;
}
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

<table style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
    <tr>
        <td width="20%"></td>
        <td width="60%" align="center" style="vertical-align: middle;">
            <div class="header">
                <h1>ORDEN DE PRODUCCIÓN</h1>
            </div>
            <img src="{{ public_path('archivos/logo.png') }}" style="width:180px;">
        </td>
        <td width="20%" align="right" style="vertical-align: top;">
            @if(isset($qrImage))
                <img src="data:image/png;base64,{{ $qrImage }}" style="width:90px; height:90px; border: 1px solid #ccc; padding: 2px;">
                <div style="font-size: 8px; text-align: center; margin-top: 2px; color: #666;">Escanear para interactuar</div>
            @endif
        </td>
    </tr>
</table>

<table class="datos">
<tr>
<td width="50%"><b>Cliente:</b> {{ $proyecto['cliente_nombre'] }}</td>
<td width="50%"><b>Fecha Emisión:</b> {{ date('d/m/Y') }}</td>
</tr>
<tr>
<td><b>Proyecto:</b> {{ $proyecto['nombre_proyecto'] }}</td>
<td><b>Vendedor:</b> {{ $proyecto['vendedor_nombre'] ?? 'N/A' }}</td>
</tr>
</table>

<table class="tabla">
<thead>
<tr>
<th width="10%">Cant.</th>
<th width="45%">Artículo</th>
<th width="45%">Especificaciones (Dimensiones / Materiales)</th>
</tr>
</thead>
<tbody>
@foreach($articulos as $item)
<tr>
<td align="center" style="font-size: 14px; font-weight: bold;">
{{ $item['cantidad'] }}
</td>
<td>
<b>{{ $item['nombre'] }}</b><br>
<span style="color:#666; font-size: 8px;">ID: {{ $item['id_articulo_produccion'] }}</span><br><br>
{{ $item['descripcion'] }}
</td>
<td>
<b>Dimensiones:</b> {{ 0 + number_format((float)$item['alto'], 2, '.', '') }} x {{ 0 + number_format((float)$item['ancho'], 2, '.', '') }} x {{ 0 + number_format((float)$item['profundo'], 2, '.', '') }} cm <br>
<b>Peso:</b> {{ 0 + number_format((float)$item['peso'], 2, '.', '') }} kg
</td>
</tr>
@endforeach
</tbody>
</table>

<div class="page-footer">
CASA TAPIER S.A. DE C.V. - Orden de Producción Interna
</div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cotización #{{ $cotizacionId }}</title>

<style>

@page {
    size: A4 landscape;
    margin: 20px;
}

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:12px;
    color:#000;
}

/* TABLA PRINCIPAL */
.table-main{
    width:100%;
    border-collapse:collapse;
}

.table-main th{
    border:1px solid #000;
    background:#f2f2f2;
    padding:6px;
}

.table-main td{
    border:1px solid #000;
    padding:6px;
}

.center{
    text-align:center;
}

.right{
    text-align:right;
}

/* IMAGEN ARTICULO */
.img-product{
    width:90px;
    height:90px;
    object-fit:cover;
}

/* TABLA TOTALES */

.totales{
    width:350px;
    margin-left:auto;
    border-collapse:collapse;
}

.totales td{
    border:1px solid #000;
    padding:6px;
}

.total-final{
    font-weight:bold;
    font-size:14px;
}

/* HEADER INFO */

.info td{
    padding:4px;
}

.titulo{
    font-size:28px;
    font-weight:bold;
}

</style>

</head>

<body>

<!-- LOGO Y TITULO -->
<table style="width:100%">
<tr>

<td width="25%">
<!-- ESPACIO LOGO -->
<img src="{{ public_path('logo.png') }}" class="logo">
</td>

<td width="50%" class="center">
<div class="titulo">CASA TAPIER</div>
</td>

<td width="25%" class="right">
Fecha: {{ date('d/m/Y') }}
</td>

</tr>
</table>


<br>

<!-- DATOS CLIENTE -->

<table class="info" width="100%">
<tr>
<td width="33%">
<strong>Nombre:</strong> {{ $proyecto['cliente_nombre'] }}
</td>

<td width="33%">
<strong>Teléfono:</strong> {{ $proyecto['telefono'] }}
</td>

<td width="33%">
<strong>Correo:</strong> {{ $proyecto['correo'] }}
</td>
</tr>

<tr>
<td colspan="3">
<strong>Dirección:</strong> {{ $proyecto['direccion'] }}
</td>
</tr>
</table>

<br>

<!-- MENSAJE -->

<p style="text-align:center; font-weight:bold;">
ESTIMADO CLIENTE, A CONTINUACIÓN LE PRESENTAMOS LA COTIZACIÓN DE LOS PRODUCTOS SOLICITADOS.
</p>


<br>

<!-- TABLA ARTICULOS -->

<table class="table-main">

<thead>
<tr>

<th width="5%">Cant.</th>

<th width="45%">Descripción del artículo</th>

<th width="15%">Imagen</th>

<th width="15%">Precio Unitario</th>

<th width="15%">Total</th>

</tr>
</thead>

<tbody>

@foreach($articulos as $item)

<tr>

<td class="center">
{{ $item['cantidad'] }}
</td>

<td>

<strong>{{ $item['nombre'] }}</strong>

<br>

{{ $item['descripcion'] }}

<br><br>

Dimensiones:
{{ $item['alto'] }} x {{ $item['ancho'] }} x {{ $item['profundo'] }} cm

</td>

<td class="center">

@if(!empty($item['imagen']))
<img src="{{ public_path('storage/'.$item['imagen']) }}" class="img-product">
@endif

</td>

<td class="right">
$ {{ number_format($item['precio_unitario'],2) }}
</td>

<td class="right">
$ {{ number_format($item['cantidad'] * $item['precio_unitario'],2) }}
</td>

</tr>

@endforeach

</tbody>

</table>

<br><br>

<!-- TOTALES -->

<table class="totales">

<tr>
<td>SUBTOTAL PRODUCTOS</td>
<td class="right">
$ {{ number_format($totales['subtotal_articulos'],2) }}
</td>
</tr>

<tr>
<td>ENVÍO</td>
<td class="right">
$ {{ number_format($totales['envio'],2) }}
</td>
</tr>

<tr>
<td>DESCUENTO</td>
<td class="right">
$ {{ number_format($totales['descuento'],2) }}
</td>
</tr>

<tr>
<td>IVA</td>
<td class="right">
$ {{ number_format($totales['iva'],2) }}
</td>
</tr>

<tr class="total-final">
<td>TOTAL</td>
<td class="right">
$ {{ number_format($totales['total'],2) }}
</td>
</tr>

</table>


<br><br>

<p style="text-align:center;">
AGRADECEMOS SU ATENCIÓN QUEDANDO A SU ENTERO SERVICIO.
</p>

</body>
</html>
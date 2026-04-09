<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
@page {
    size: landscape;
    margin: 1cm;
}

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11px;
    color: #000;
}

.header-table {
    width: 100%;
    margin-bottom: 10px;
    border-collapse: collapse;
}
.header-table td {
    padding: 5px;
    border: 1px solid #ccc;
}

.tabla {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.tabla th {
    border: 1px solid #000;
    background: #e6e6e6;
    padding: 5px;
    font-size: 10px;
    text-align: center;
}
.tabla td {
    border: 1px solid #000;
    padding: 5px;
    font-size: 10px;
    vertical-align: top;
}

.signature-box {
    margin-top: 30px;
    font-size: 11px;
}

.bg-gray {
    background: #e6e6e6;
}
</style>
</head>
<body>

    <h2 style="text-align: center; margin: 0 0 10px 0; font-size: 16px;">ACTA DE ENTREGA</h2>

    <table class="header-table">
        <tr>
            <td width="15%" class="bg-gray"><b>FECHA:</b></td>
            <td width="35%">{{ date('d/m/Y') }}</td>
            <td width="15%" class="bg-gray"><b>TELÉFONO:</b></td>
            <td width="35%">{{ $proyecto['telefono'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bg-gray"><b>PROYECTO:</b></td>
            <td>{{ mb_strtoupper($proyecto['cliente_nombre_corto']) }}, {{ $proyecto['proyecto_id'] }}</td>
            <td class="bg-gray"><b>CORREO:</b></td>
            <td>{{ $proyecto['correo'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bg-gray"><b>PARTIDA:</b></td>
            <td>Diseño y Ventas | Logística</td>
            <td colspan="2" class="bg-gray"></td>
        </tr>
    </table>

    <table class="tabla">
        <thead>
            <tr>
                <th width="5%">Cant.</th>
                <th width="45%">Descripción del artículo (Dimensiones en cm. ↔↕↗)</th>
                <th width="15%">Imagen</th>
                <th width="5%">I<br><span style="font-size:7px; font-weight:normal;">(Instalación)</span></th>
                <th width="5%">D<br><span style="font-size:7px; font-weight:normal;">(Desemplaye)</span></th>
                <th width="5%">M<br><span style="font-size:7px; font-weight:normal;">(Maniobraje)</span></th>
                <th width="20%">Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articulos as $item)
            <tr>
                <td style="text-align:center; vertical-align: middle;">{{ $item['cantidad'] }}</td>
                <td>
                    <b>{{ $item['nombre'] }}</b><br>
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
                            
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)($item['cubicaje'] ?? 0), 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)($item['cubicaje'] ?? 0) * $item['cantidad'], 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">{{ 0 + number_format((float)($item['peso'] ?? 0), 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf; border-right: 1px solid #ccc;">-</td>
                            <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)($item['peso'] ?? 0) * $item['cantidad'], 1, '.', '') }}</td>
                        </tr>
                    </table>
                    <br>
                    {{ $item['descripcion'] }}
                </td>
                <td align="center" style="vertical-align: middle;">
                    @php
                        $imagePath = null;
                        if (!empty($item['imagen'])) {
                            $rutaRelativa = preg_match('/storage\/(.*)$/', $item['imagen'], $m) ? $m[1] : $item['imagen'];
                            $local = public_path('storage/' . $rutaRelativa);
                            if (file_exists($local)) {
                                $imagePath = $local;
                            }
                        }
                    @endphp
                    @if($imagePath)
                        <img src="{{ $imagePath }}" style="width:60px; height:60px; object-fit:contain; display:block; margin:0 auto;">
                    @else
                        N/A
                    @endif
                </td>
                <td style="text-align:center; vertical-align: middle;">{{ $proyecto['requiere_instalacion'] ? 'Sí' : 'No' }}</td>
                <td style="text-align:center; vertical-align: middle;">{{ $proyecto['requiere_desemplaye'] ? 'Sí' : 'No' }}</td>
                <td style="text-align:center; vertical-align: middle;">{{ $proyecto['requiere_maniobraje'] ? 'Sí' : 'No' }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <b>Dirección:</b> {{ $proyecto['direccion'] ?? 'N/A' }}
    </div>

    <div class="signature-box">
        <p style="margin-bottom: 20px;">
            Yo: &nbsp;____________________________________________________________________________________________________________________________________________<br>
            <span style="font-size: 9px; color: #555;">Firmo esta hoja para confirmar la entrega de los productos enlistados.</span>
        </p>

        <p style="margin-top: 40px;">
            ____________________________________________________________________________________________________________________________________________<br>
            <span style="font-size: 9px; color: #555;">Firma de la persona que entrega con satisfacción los productos.</span>
        </p>
    </div>

</body>
</html>
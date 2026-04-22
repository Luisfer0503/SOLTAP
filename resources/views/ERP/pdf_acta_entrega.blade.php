<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
@page {
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

.tabla td table td {
    border: none;
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
    
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 10px;">
        <tr>
            <td style="width: 25%; border: none; padding: 0; text-align: left;">
                <img src="{{ public_path('archivos/logo.png') }}" style="width: 150px;">
            </td>
            <td style="width: 50%; border: none; padding: 0; text-align: center; vertical-align: middle;">
                <h2 style="margin: 0; font-size: 16px;">ACTA DE ENTREGA</h2>
            </td>
            <td style="width: 25%; border: none; padding: 0;"></td>
        </tr>
    </table>

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
            <td>{{ $partida ?? ($proyecto['partida_acta'] ?? '') }}</td>
            <td colspan="2" style="padding: 0; vertical-align: middle; border-bottom: none;">
                <table style="width: 100%; border-collapse: collapse; border: none; margin: 0;">
                    <tr>
                        <td style="border: none; padding: 5px; text-align: center; width: 33.33%;">
                            <table style="margin: 0 auto; border: none; border-collapse: collapse;">
                                <tr>
                                    <td style="border: none; padding: 0 5px 0 0; font-size: 12px;"><b>I</b></td>
                                    <td style="border: none; padding: 0;">
                                        <div style="width:14px; height:14px; border:1px solid #000; line-height: 14px; text-align: center; font-weight: bold; font-size: 11px;">
                                            {!! $proyecto['requiere_instalacion'] ? '&#10003;' : '&nbsp;' !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="border: none; padding: 5px; text-align: center; width: 33.33%;">
                            <table style="margin: 0 auto; border: none; border-collapse: collapse;">
                                <tr>
                                    <td style="border: none; padding: 0 5px 0 0; font-size: 12px;"><b>D</b></td>
                                    <td style="border: none; padding: 0;">
                                        <div style="width:14px; height:14px; border:1px solid #000; line-height: 14px; text-align: center; font-weight: bold; font-size: 11px;">
                                            {!! $proyecto['requiere_desemplaye'] ? '&#10003;' : '&nbsp;' !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="border: none; padding: 5px; text-align: center; width: 33.33%;">
                            <table style="margin: 0 auto; border: none; border-collapse: collapse;">
                                <tr>
                                    <td style="border: none; padding: 0 5px 0 0; font-size: 12px;"><b>M</b></td>
                                    <td style="border: none; padding: 0;">
                                        <div style="width:14px; height:14px; border:1px solid #000; line-height: 14px; text-align: center; font-weight: bold; font-size: 11px;">
                                            {!! $proyecto['requiere_maniobraje'] ? '&#10003;' : '&nbsp;' !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td style="text-align: center; font-weight: bold; border: 1px solid #ccc; background: #e6e6e6; padding: 5px; font-size: 11px;">
                Diseño y Ventas | Logística
            </td>
        </tr>
    </table>

    <table class="tabla">
        <thead>
            <tr>
                <th width="6%">Cant.</th>
                <th width="50%">Descripción del artículo (Dimensiones en cm. ↔↕↗)</th>
                <th width="15%">Imagen</th>
                <th width="29%">Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articulos as $item)
            <tr>
                <td style="text-align:center; vertical-align: middle;">{{ $item['cantidad'] }}</td>
                <td>
                    <b>{{ $item['nombre'] }}</b><br>
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
                            
                            <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)($item['cubicaje'] ?? 0), 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf;">-</td>
                            <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)($item['cubicaje'] ?? 0) * $item['cantidad'], 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf;">-</td>
                            <td style="padding: 2px; color:#1b3fbf;">{{ 0 + number_format((float)($item['peso'] ?? 0), 1, '.', '') }}</td>
                            <td style="padding: 2px; color:#1b3fbf;">-</td>
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
                <td>{{ $item['comentarios_acta'] ?? '' }}</td>
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
<!-- SALTO DE PÁGINA PARA TÉRMINOS Y CONDICIONES -->
<div style="page-break-before: always;"></div>

<div style="width: 100%; text-align: center; margin-top: 0px; margin-bottom: 10px;">
    <img src="{{ public_path('archivos/logo.png') }}" style="width: 140px; margin-bottom: 5px;">
    <h2 style="font-size: 14px; font-weight: bold; color: #1b3fbf; margin: 0; text-transform: uppercase;">Términos y Condiciones</h2>
</div>

<div style="width: 100%; margin-top: 10px;">
    @if(isset($terminos) && count($terminos) > 0)
        @foreach($terminos as $termino)
            <div style="margin-bottom: 8px;">
                <h3 style="font-size: 9px; font-weight: bold; margin-bottom: 2px; color: #1b3fbf; text-transform: uppercase;">{{ $termino->nombre ?? '' }}</h3>
                <div style="font-size: 7.5px; text-align: justify; line-height: 1.2; color: #333;">
                    {!! nl2br(e($termino->contenido ?? '')) !!}
                </div>
            </div>
        @endforeach
    @endif
</div>

</body>
</html>
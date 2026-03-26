<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial del Proyecto</title>
<style>
    @page { margin: 1.5cm 1cm 2cm 1cm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #333; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1b3fbf; padding-bottom: 10px; }
    .header h1 { font-size: 16px; margin: 0; color: #000; text-transform: uppercase;}
    .logo { width: 150px; margin-bottom: 10px; }
    .datos { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .datos td { padding: 6px; border-bottom: 1px solid #e0e0e0; font-size: 11px;}
    .datos b { font-weight: bold; color: #000; }
    .tabla { width: 100%; border-collapse: collapse; margin-top: 10px;}
    .tabla th { background-color: #1b3fbf; color: #fff; border: 1px solid #1b3fbf; padding: 8px; font-size: 10px; text-align: left; }
    .tabla td { border: 1px solid #ccc; padding: 8px; font-size: 10px; vertical-align: top; }
    .page-footer { position: fixed; bottom: -1.5cm; left: 0; right: 0; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ccc; padding-top: 5px;}
</style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('archivos/logo.png') }}" class="logo">
    <h1>Historial de Interacciones del Proyecto</h1>
</div>

<table class="datos">
    <tr>
        <td width="50%"><b>Proyecto:</b> {{ $proyecto->nombre_proyecto }}</td>
        <td width="50%"><b>Fecha de Impresión:</b> {{ date('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <td width="50%"><b>Cliente:</b> {{ $proyecto->cliente_nombre ?? 'No asignado' }}</td>
    </tr>
</table>

<table class="tabla">
    <thead>
        <tr>
            <th width="15%">Fecha y Hora</th>
            <th width="25%">Interacción</th>
            <th width="20%">Usuario Responsable</th>
            <th width="40%">Comentarios</th>
        </tr>
    </thead>
    <tbody>
        @forelse($historial as $item)
            <tr>
                <td>{{ $item->fecha_formateada }}</td>
                <td><b>{{ $item->interaccion_nombre }}</b></td>
                <td>{{ $item->usuario_nombre }}</td>
                <td>{{ $item->comentarios }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" align="center" style="padding: 15px;">No hay interacciones registradas para este proyecto.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="page-footer">
    CASA TAPIER S.A. DE C.V. - Documento de control interno
</div>

</body>
</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InicioController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month'); // month, week, year

        switch ($filter) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default: // month
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
        }

        $startDateStr = $startDate->format('Y-m-d H:i:s');
        $endDateStr = $endDate->format('Y-m-d H:i:s');

        // Ventas totales
        $totalSalesCT = DB::table('cotizaciones')->whereBetween('created_at', [$startDateStr, $endDateStr])->sum('total');
        $totalSalesSH = DB::table('cotizaciones_solferino')->whereBetween('created_at', [$startDateStr, $endDateStr])->sum('total');
        $totalSales = $totalSalesCT + $totalSalesSH;

        // Tasa de conversión leads
        $totalLeads = DB::table('prospectos')->whereBetween('fecha', [$startDateStr, $endDateStr])->count();
        $totalCotizacionesCT = DB::table('cotizaciones')->whereBetween('created_at', [$startDateStr, $endDateStr])->count();
        $totalCotizacionesSH = DB::table('cotizaciones_solferino')->whereBetween('created_at', [$startDateStr, $endDateStr])->count();
        $totalCotizaciones = $totalCotizacionesCT + $totalCotizacionesSH;
        
        $conversionRate = $totalLeads > 0 ? ($totalCotizaciones / $totalLeads) * 100 : 0;

        // Valor promedio de remisiones
        $avgRemision = $totalCotizaciones > 0 ? $totalSales / $totalCotizaciones : 0;

        // % anticipo cobrado
        $totalPagos = DB::table('plan_pagos')->whereBetween('created_at', [$startDateStr, $endDateStr])->sum('monto_pagado');
        $anticipoPercentage = $totalSales > 0 ? ($totalPagos / $totalSales) * 100 : 0;

        // Gráfica lineal de ventas reales
        $salesCT = DB::table('cotizaciones')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', [$startDateStr, $endDateStr])
            ->groupBy('date')
            ->get();
            
        $salesSH = DB::table('cotizaciones_solferino')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', [$startDateStr, $endDateStr])
            ->groupBy('date')
            ->get();
            
        $salesMap = [];
        foreach ($salesCT as $s) { $salesMap[$s->date] = $s->total; }
        foreach ($salesSH as $s) { 
            if (isset($salesMap[$s->date])) { $salesMap[$s->date] += $s->total; }
            else { $salesMap[$s->date] = $s->total; }
        }
        ksort($salesMap);
        
        $salesDates = array_keys($salesMap);
        $salesTotals = array_values($salesMap);

        // Gráfica de barras por enfoques
        $enfoquesData = DB::table('enfoques')
            ->leftJoin('proyecto_detalles', 'enfoques.enfoque_id', '=', 'proyecto_detalles.enfoque_id')
            ->leftJoin('cotizaciones', 'proyecto_detalles.detalles_id', '=', 'cotizaciones.proyecto_id')
            ->select('enfoques.nombre', DB::raw('SUM(cotizaciones.total) as total'))
            ->whereBetween('cotizaciones.created_at', [$startDateStr, $endDateStr])
            ->groupBy('enfoques.enfoque_id', 'enfoques.nombre')
            ->get();
            
        $enfoquesDataSH = DB::table('enfoques')
            ->leftJoin('proyecto_detalles', 'enfoques.enfoque_id', '=', 'proyecto_detalles.enfoque_id')
            ->leftJoin('cotizaciones_solferino', 'proyecto_detalles.detalles_id', '=', 'cotizaciones_solferino.proyecto_id')
            ->select('enfoques.nombre', DB::raw('SUM(cotizaciones_solferino.total) as total'))
            ->whereBetween('cotizaciones_solferino.created_at', [$startDateStr, $endDateStr])
            ->groupBy('enfoques.enfoque_id', 'enfoques.nombre')
            ->get();
            
        $enfoquesMap = [];
        $allEnfoques = DB::table('enfoques')->get();
        foreach ($allEnfoques as $e) { $enfoquesMap[$e->nombre] = 0; }
        
        foreach ($enfoquesData as $e) { $enfoquesMap[$e->nombre] += (float) $e->total; }
        foreach ($enfoquesDataSH as $e) { $enfoquesMap[$e->nombre] += (float) $e->total; }
        
        $enfoquesLabels = array_keys($enfoquesMap);
        $enfoquesTotals = array_values($enfoquesMap);

        // Gráfica de barras por categorías
        $categoriesData = DB::table('categorias_articulos')
            ->leftJoin('proyecto_articulos', 'categorias_articulos.categoria_articulo_id', '=', 'proyecto_articulos.categoria_id')
            ->leftJoin('cotizaciones', 'proyecto_articulos.proyecto_id', '=', 'cotizaciones.proyecto_id')
            ->select('categorias_articulos.nombre', DB::raw('SUM(proyecto_articulos.cantidad * proyecto_articulos.precio) as total'))
            ->whereBetween('cotizaciones.created_at', [$startDateStr, $endDateStr])
            ->groupBy('categorias_articulos.categoria_articulo_id', 'categorias_articulos.nombre')
            ->get();
            
        $categoriesDataSH = DB::table('categorias_articulos')
            ->leftJoin('proyecto_articulos', 'categorias_articulos.categoria_articulo_id', '=', 'proyecto_articulos.categoria_id')
            ->leftJoin('cotizaciones_solferino', 'proyecto_articulos.proyecto_id', '=', 'cotizaciones_solferino.proyecto_id')
            ->select('categorias_articulos.nombre', DB::raw('SUM(proyecto_articulos.cantidad * proyecto_articulos.precio) as total'))
            ->whereBetween('cotizaciones_solferino.created_at', [$startDateStr, $endDateStr])
            ->groupBy('categorias_articulos.categoria_articulo_id', 'categorias_articulos.nombre')
            ->get();
            
        $catMap = [];
        $allCats = DB::table('categorias_articulos')->get();
        foreach ($allCats as $c) { $catMap[$c->nombre] = 0; }
        
        foreach ($categoriesData as $c) { $catMap[$c->nombre] += (float) $c->total; }
        foreach ($categoriesDataSH as $c) { $catMap[$c->nombre] += (float) $c->total; }
        
        $catLabels = array_keys($catMap);
        $catTotals = array_values($catMap);

        return view('inicio', compact(
            'totalSales', 'conversionRate', 'avgRemision', 'anticipoPercentage',
            'salesDates', 'salesTotals', 'enfoquesLabels', 'enfoquesTotals', 'catLabels', 'catTotals', 'filter'
        ));
    }
}
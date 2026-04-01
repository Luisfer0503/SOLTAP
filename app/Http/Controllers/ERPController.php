<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendedor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage as FacadeStorage;
use Barryvdh\DomPDF\Facade\Pdf;


class ERPController extends Controller
{
    
      public function inicio()
      {
            return view('inicio');
      }

      public function altaArticulos()
      {     
            $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
            $role = strtoupper($userRoleName);
            if (!in_array($role, ['ADMIN', 'VENDEDOR/DISEÑADOR'])) {
                return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
            }

            $proyectos = DB::table('Proyectos')->select('proyecto_id as id', 'nombre')->orderBy('proyecto_id', 'desc')->get();
            $categorias = DB::table('categorias_articulos')->select('categoria_id', 'nombre')->orderBy('nombre')->get();
            $articulos = DB::table('articulos')->select('articulo_id', 'nombre', 'categoria_id')->orderBy('nombre')->get();
            
            // Datos para materiales dinámicos
            $materiales = DB::table('materiales')->orderBy('nombre')->get();
            $submateriales = DB::table('submateriales')->orderBy('nombre')->get();
            $chapas = DB::table('chapas')->orderBy('nombre')->get();
            $proveedores = DB::table('proveedores')->orderBy('nombre')->get();

            return view('ERP.gestionArticulos', compact('proyectos', 'categorias', 'articulos', 'materiales', 'submateriales', 'chapas', 'proveedores'));
      }

      public function gestionArticulos()
      {
            $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
            $role = strtoupper($userRoleName);
            if (!in_array($role, ['ADMIN', 'VENDEDOR/DISEÑADOR'])) {
                return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
            }

            $proyectos = DB::table('Proyectos')->select('proyecto_id as id', 'nombre')->orderBy('proyecto_id', 'desc')->get();
            $categorias = DB::table('categorias_articulos')->select('categoria_articulo_id', 'nombre')->orderBy('nombre')->get();
            $articulos = DB::table('articulos')->select('articulo_id', 'nombre', 'categoria_articulo')->orderBy('nombre')->get();
            
            // Datos para materiales dinámicos
            $materiales = DB::table('materiales')->orderBy('nombre')->get();
            $submateriales = DB::table('submateriales')->orderBy('nombre')->get();
            $chapas = DB::table('chapas')->orderBy('nombre')->get();
            $proveedores = DB::table('proveedores')->orderBy('nombre')->get();

            return view('ERP.gestionArticulos', compact('proyectos', 'categorias', 'articulos', 'materiales', 'submateriales', 'chapas', 'proveedores'));
      }


      public function seguimientoProyectos(Request $request)
      {
          $query = DB::table('Proyectos')
              ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
              ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
              ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
              ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
              ->leftJoin('vendedores', 'proyecto_detalles.vendedor_id', '=', 'vendedores.vendedor_id')
              ->leftJoin('empresas', 'proyecto_detalles.empresa_id', '=', 'empresas.empresa_id')
              ->select(
                  'Proyectos.proyecto_id as id',
                  'Proyectos.nombre',
                  'Proyectos.estatus',
                  'Proyectos.created_at',
                  'empresas.nombre as empresa_nombre',
                  DB::raw("CONCAT_WS(' ', vendedores.nombre, vendedores.apellido_paterno) as disenador"),
                  DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno), '')) as cliente")
              );

          $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
          $role = strtoupper($userRoleName);

          $rolesProduccionCoords = ['SUP. CARPINTERÍA', 'SUP. CARPINTERIA', 'SUP. BARNIZ Y LIJADO', 'COORD. INSTALACIÓN / SUP. HERRERÍA', 'COORD. INSTALACION / SUP. HERRERIA', 'COORD. PRODUCCIÓN/COMPRAS', 'COORD. PRODUCCION/COMPRAS'];
          $rolesProduccionScan = ['PRODUCCIÓN', 'PRODUCCION', 'LOGÍSTICA', 'LOGISTICA', 'ALMACÉN', 'ALMACEN', 'COORD. INSTALACIÓN', 'COORD. INSTALACION'];          
          if (!in_array($role, array_merge(['ADMIN', 'VENDEDOR/DISEÑADOR', 'DIRECCIÓN', 'DIRECCION', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA', 'COORD. DV&MKT', 'COORD. DV SOLFERINO'], $rolesProduccionCoords, $rolesProduccionScan))) {
              return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
          }

          if ($request->has('proyecto_id')) {
              $query->where('Proyectos.proyecto_id', $request->query('proyecto_id'));
          } else {
              if (in_array($role, $rolesProduccionScan)) {
                  $query->whereRaw('1 = 0'); // Restricción: Solo puede visualizar información del proyecto que escanea
              }
          }

          // Restricción: Solo mostrar proyectos que tengan artículos asignados
          $query->whereExists(function ($q) {
              $q->select(DB::raw(1))
                ->from('proyecto_articulos')
                ->whereColumn('proyecto_articulos.proyecto_id', 'Proyectos.proyecto_id');
          });

          $proyectos = $query->orderBy('Proyectos.proyecto_id', 'desc')->get();

          // Eager load articles for these projects to display progress without expanding
          $projectIds = $proyectos->pluck('id');
          $articulos = DB::table('proyecto_articulos')
              ->whereIn('proyecto_id', $projectIds)
              ->select('id', 'proyecto_id', 'articulo_produccion_id', 'nombre', 'descripcion', 'cantidad', 'imagen', 'produccion', 'dyv', 'logistica')
              ->get();

          // Attach articles to projects
          foreach ($proyectos as $p) {
              $p->articulos = $articulos->where('proyecto_id', $p->id)->values()->map(function($art){
                  if ($art->imagen) {
                      $art->imagen = asset('storage/' . $art->imagen);
                  }
                  return $art;
              });
          }
  
          // Obtenemos los usuarios y sus áreas para los campos ORIGINÓ y RESOLVIÓ
          $usuarios = DB::table('users')
              ->leftJoin('areas', 'users.area', '=', 'areas.id')
              ->select('users.id', 'users.name', 'areas.nombre as area_name')
              ->orderBy('users.name', 'asc')
              ->get();

          $categoriasFallas = [];
          if (\Illuminate\Support\Facades\Schema::hasTable('categorias_fallas')) {
              $categoriasFallas = DB::table('categorias_fallas')->get();
          }
          
          $subcategoriasFallas = [];
          if (\Illuminate\Support\Facades\Schema::hasTable('subcategoria_fallas')) {
              $subcategoriasFallas = DB::table('subcategoria_fallas')->get();
          }

          $interacciones = collect();
          if (\Illuminate\Support\Facades\Schema::hasTable('interacciones')) {
              $interacciones = DB::table('interacciones')->get()->filter(function($int) {
                  $id = $int->id ?? $int->interaccion_id;
                  return $id >= 14 && $id <= 50;
              })->values();
          }

          return view('ERP.seguimientoProyectos', compact('proyectos', 'usuarios', 'categoriasFallas', 'subcategoriasFallas', 'interacciones'));
      }

      public function guardarVerificacionArticulos(Request $request)
      {
          try {
              $proyectoId = $request->input('proyecto_id');
              $articulos = $request->input('articulos');
              
              // 1. Actualizar cada artículo asegurándonos de convertir correctamente el valor booleano
              foreach ($articulos as $art) {
                  $p = (isset($art['produccion']) && filter_var($art['produccion'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;
                  $dv = (isset($art['dyv']) && filter_var($art['dyv'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;
                  $l = (isset($art['logistica']) && filter_var($art['logistica'], FILTER_VALIDATE_BOOLEAN)) ? 1 : 0;

                  DB::table('proyecto_articulos')
                      ->where('id', $art['id'])
                      ->update([
                          'produccion' => $p,
                          'dyv' => $dv,
                          'logistica' => $l,
                      ]);
              }

              // 2. Verificar el 100% directamente desde la base de datos (Garantiza exactitud)
              if ($proyectoId) {
                  $totales = DB::table('proyecto_articulos')
                      ->where('proyecto_id', $proyectoId)
                      ->selectRaw('COUNT(id) as total, SUM(produccion) as sum_p, SUM(dyv) as sum_dv, SUM(logistica) as sum_l')
                      ->first();

                  if ($totales && $totales->total > 0) {
                      $this->verificarYRegistrarLiberacion($proyectoId, 'LIBERACION PRODUCCION', $totales->sum_p == $totales->total);
                      $this->verificarYRegistrarLiberacion($proyectoId, 'LIBERACION DISEÑO & VENTAS', $totales->sum_dv == $totales->total);
                      $this->verificarYRegistrarLiberacion($proyectoId, 'LIBERACION LOGISTICA', $totales->sum_l == $totales->total);
                  }
              }

              return response()->json(['success' => true]);
          } catch (\Exception $e) {
              return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
          }
      }

      private function verificarYRegistrarLiberacion($proyectoId, $nombreInteraccion, $is100Percent)
      {
          if (!$is100Percent) return;

          if (\Illuminate\Support\Facades\Schema::hasTable('interacciones') && \Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
              
              // Buscar el ID de la interacción en el catálogo
              $interaccion = DB::table('interacciones')->where('nombre', $nombreInteraccion)->first();
              
              // Fallback: por si existe un espacio de más o un & que no cuadre exacto
              if (!$interaccion) {
                  $searchName = str_replace([' ', '&'], '%', $nombreInteraccion);
                  $interaccion = DB::table('interacciones')->where('nombre', 'LIKE', "%{$searchName}%")->first();
              }

              // Solo guardarla si se encontró en el catálogo (evita fallos de SQL si le pasas un texto a un campo entero)
              if ($interaccion) {
                  $interaccionId = $interaccion->id ?? $interaccion->interaccion_id;

                  $exists = DB::table('proyecto_interacciones')
                      ->where('proyecto_id', $proyectoId)
                      ->where('interaccion_id', $interaccionId)
                      ->exists();

                  if (!$exists) {
                      DB::table('proyecto_interacciones')->insert([
                          'proyecto_id' => $proyectoId,
                          'interaccion_id' => $interaccionId,
                          'user_id' => auth()->id(),
                          'comentarios' => "El proyecto alcanzó el 100% en la etapa de " . $nombreInteraccion . ".",
                          'created_at' => now(),
                          'updated_at' => now()
                      ]);
                  }
              }
          }
      }

      public function guardarFalla(Request $request)
      {
          try {
              // El registro en proyecto_interacciones sí existe y se guardará como historial global

              $reportePath = null;
              if ($request->hasFile('reporte')) {
                  $file = $request->file('reporte');
                  $filename = time() . '_falla_' . $file->getClientOriginalName();
                  FacadeStorage::disk('public')->put($filename, \File::get($file));
                  $reportePath = $filename;
              }

              // Guardar el registro principal en la nueva tabla 'fallas'
              $fallaId = DB::table('fallas')->insertGetId([
                  'proyecto_id' => $request->proyecto_id,
                  'articulo_id' => $request->articulo_id,
                  'fecha' => $request->fecha,
                  'sem' => $request->sem,
                  'mes' => $request->mes,
                  'cantidad' => $request->cantidad,
                  'categoria_id' => $request->falla_categoria ?: null,
                  'subcategoria_id' => $request->falla_subcategoria ?: null,
                  'descripcion' => $request->descripcion,
                  'hh_minutos' => $request->hh_minutos,
                  'costo_hh' => $request->costo_hh,
                  'costo_materiales' => $request->costo_materiales,
                  'costo_total' => $request->costo_total,
                  'origino' => $request->origino, 
                  'resolvio' => $request->resolvio, 
                  'materiales' => $request->materiales, 
                  'reporte_imagen' => $reportePath,
                  'registrado_por' => auth()->id(),
                  'created_at' => now()
              ]);

              // Registro la interacción de la falla para el historial del proyecto.
              if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                  $interaccion = DB::table('interacciones')->where('nombre', 'REPORTE DE FALLA')->first();
                  $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'REPORTE DE FALLA') : 'REPORTE DE FALLA';

                  $categoriaNombre = $request->falla_categoria;
                  if (is_numeric($categoriaNombre) && \Illuminate\Support\Facades\Schema::hasTable('categorias_fallas')) {
                      $cat = DB::table('categorias_fallas')->where('id', $categoriaNombre)->first();
                      if ($cat) {
                          $categoriaNombre = $cat->nombre;
                      }
                  }

                  DB::table('proyecto_interacciones')->insert([
                      'proyecto_id' => $request->proyecto_id,
                      'interaccion_id' => $interaccionId,
                      'user_id' => auth()->id(),
                      'comentarios' => "Reporte de falla en el artículo ID " . $request->articulo_id . ".\nCategoría: " . $categoriaNombre . ".\nDescripción: " . $request->descripcion . ".\nCosto Total Asignado: $" . $request->costo_total,
                      'created_at' => now(),
                      'updated_at' => now()
                  ]);
              }

              return response()->json(['success' => true, 'message' => 'Falla reportada y guardada correctamente.']);
          } catch (\Exception $e) {
              return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
          }
      }


    public function asignacionPrecios()
    {
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        if (!in_array($role, ['ADMIN', 'VENDEDOR/DISEÑADOR', 'COORD. DV SOLFERINO', 'COORD. DV&MKT', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA'])) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
        }

        $proyectos = DB::table('Proyectos')
            ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
            ->leftJoin('vendedores', 'proyecto_detalles.vendedor_id', '=', 'vendedores.vendedor_id')
            ->select(
                'Proyectos.proyecto_id',
                'Proyectos.prospecto_id',
                'Proyectos.cliente_id',
                'Proyectos.nombre as nombre_proyecto',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                DB::raw("COALESCE(P1.telefono, P2.telefono) as telefono"),
                DB::raw("COALESCE(P1.correo, P2.correo) as correo"),
                DB::raw("COALESCE(proyecto_detalles.direccion_entrega, NULLIF(CONCAT_WS(', ', COALESCE(P1.calle, P2.calle), COALESCE(P1.municipio, P2.municipio)), '')) as direccion"),
                'Proyectos.created_at as fecha',
                DB::raw("CONCAT_WS(' ', vendedores.nombre, vendedores.apellido_paterno) as vendedor_nombre"),
                DB::raw("COALESCE(P1.iva, P2.iva, 0) as iva_porcentaje"),
                DB::raw("COALESCE(P1.descuento, P2.descuento, 0) as descuento_porcentaje"),
                'proyecto_detalles.rfc',
                'proyecto_detalles.condiciones_pago',
                DB::raw("(SELECT COUNT(*) FROM cotizaciones WHERE cotizaciones.proyecto_id = Proyectos.proyecto_id AND total > 0) as tiene_cotizacion"),
                DB::raw("(SELECT COUNT(*) FROM proyecto_articulos WHERE proyecto_articulos.proyecto_id = Proyectos.proyecto_id AND (precio <= 0 OR precio IS NULL)) as articulos_pendientes"),
                DB::raw("(SELECT COUNT(*) FROM plan_pagos JOIN cotizaciones ON plan_pagos.cotizacion_id = cotizaciones.cotizacion_id WHERE cotizaciones.proyecto_id = Proyectos.proyecto_id AND plan_pagos.monto_pagado > 0) as tiene_pagos")
            )
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('proyecto_articulos')
                      ->whereColumn('proyecto_articulos.proyecto_id', 'Proyectos.proyecto_id');
            })
            ->orderBy('Proyectos.proyecto_id', 'desc')
            ->get();

        return view('ERP.asignacionPrecios', compact('proyectos'));
    }

    public function generarCotizacionPdf(Request $request)
    {
        try {
            // Validar que todos los campos necesarios estén completos
            $validator = Validator::make($request->all(), [
                'articulos' => 'required|array|min:1',
                'articulos.*.precio_unitario' => 'required|numeric|min:0',
                'totales.envio' => 'required|numeric|min:0',
                'cotizacionId' => 'required|integer'
            ], [
                'articulos.*.precio_unitario.required' => 'Todos los artículos deben tener un precio asignado.',
                'totales.envio.required' => 'El costo de envío es obligatorio para generar el PDF.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $data = $request->all();
            $proyecto = $data['proyecto'];
            $articulos = $data['articulos'];
            $totales = $data['totales'];
            $cotizacionId = $data['cotizacionId'];

            // Registrar interacción de Creación de Cotización
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'CREACIÓN DE COTIZACIÓN')->first();
                $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'CREACIÓN DE COTIZACIÓN') : 'CREACIÓN DE COTIZACIÓN';

                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $proyecto['proyecto_id'],
                    'interaccion_id' => $interaccionId,
                    'user_id' => auth()->id(),
                    'comentarios' => 'Se generó el PDF de cotización por un total de $' . number_format((float)($totales['total'] ?? 0), 2),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 3. Generar PDF usando la vista
            $pdf = Pdf::loadView('ERP.pdf_cotizacion', compact('proyecto', 'articulos', 'totales', 'cotizacionId'));
            
            // Retornar el PDF como descarga
            return $pdf->download('Cotizacion_' . $proyecto['nombre_proyecto'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function generarRemisionPdf(Request $request)
    {
        try {
            $proyecto_id = $request->input('proyecto_id');
            $pagos = $request->input('pagos'); // Recibir plan de pagos
            $rfc = $request->input('rfc');
            $condiciones = $request->input('condiciones');
            
            // Guardar/Actualizar RFC y Condiciones en proyecto_detalles
            DB::table('proyecto_detalles')->updateOrInsert(
                ['detalles_id' => $proyecto_id],
                ['rfc' => $rfc, 'condiciones_pago' => $condiciones]
            );

            // 1. Obtener datos del proyecto (desde BD)
            $proyectoData = DB::table('Proyectos')
                ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
                ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
                ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
                ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
                ->leftJoin('vendedores', 'proyecto_detalles.vendedor_id', '=', 'vendedores.vendedor_id')
                ->select(
                    'Proyectos.proyecto_id',
                    'Proyectos.cliente_id',
                    'Proyectos.nombre as nombre_proyecto',
                    DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                    DB::raw("COALESCE(P1.telefono, P2.telefono) as telefono"),
                    DB::raw("COALESCE(P1.correo, P2.correo) as correo"),
                    DB::raw("COALESCE(proyecto_detalles.direccion_entrega, NULLIF(CONCAT_WS(', ', COALESCE(P1.calle, P2.calle), COALESCE(P1.municipio, P2.municipio)), '')) as direccion"),
                    'Proyectos.created_at as fecha',
                    DB::raw("CONCAT_WS(' ', vendedores.nombre, vendedores.apellido_paterno) as vendedor_nombre"),
                    DB::raw("COALESCE(P1.iva, P2.iva, 0) as iva_porcentaje")
                )
                ->where('Proyectos.proyecto_id', $proyecto_id)
                ->first();

            if (!$proyectoData) {
                return response()->json(['error' => 'Proyecto no encontrado'], 404);
            }
            
            $proyecto = (array)$proyectoData;

            // 2. Obtener artículos guardados
            $articulos = DB::table('proyecto_articulos')
                ->where('proyecto_id', $proyecto_id)
                ->select('articulo_produccion_id as id_articulo_produccion', 'nombre', 'descripcion', 'alto', 'ancho', 'profundo', 'cantidad', 'precio as precio_unitario', 'cubicaje', 'peso', 'imagen')
                ->get()
                ->map(function($item){ return (array)$item; })
                ->toArray();

            // 3. Obtener totales guardados (última cotización)
            $cotizacion = DB::table('cotizaciones')
                ->where('proyecto_id', $proyecto_id)
                ->orderBy('cotizacion_id', 'desc')
                ->first();

            $totales = [
                'subtotal_articulos' => 0,
                'envio' => 0,
                'descuento' => 0,
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
                'cubicaje' => 0,
                'peso' => 0,
                'articulos' => 0,
                'iva_porcentaje' => (float)$proyectoData->iva_porcentaje
            ];

            foreach($articulos as $art){
                $cantidad = (float)$art['cantidad'];
                $totales['subtotal_articulos'] += $cantidad * $art['precio_unitario'];
                $totales['cubicaje'] += (float)$art['cubicaje'] * $cantidad;
                $totales['peso'] += (float)$art['peso'] * $cantidad;
                $totales['articulos'] += $cantidad;
            }

            $cotizacionId = 0;
            if ($cotizacion) {
                $totales['envio'] = $cotizacion->envio;
                $totales['descuento'] = $cotizacion->descuento;
                $totales['subtotal'] = $cotizacion->subtotal;
                $totales['iva'] = $cotizacion->iva;
                $totales['total'] = $cotizacion->total;
                $cotizacionId = $cotizacion->cotizacion_id;
            }

            // 4. Guardar Plan de Pagos en BD (Si existen)
            if ($cotizacionId && !empty($pagos)) {
                // Verificar si ya existen pagos realizados para no sobrescribir historial
                $pagosRealizados = DB::table('plan_pagos')
                    ->where('cotizacion_id', $cotizacionId)
                    ->where('monto_pagado', '>', 0)
                    ->exists();

                if (!$pagosRealizados) {
                    // Limpiar pagos previos de esta cotización para evitar duplicados
                    DB::table('plan_pagos')->where('cotizacion_id', $cotizacionId)->delete();

                    $totalPagosPlan = count($pagos);

                    foreach ($pagos as $index => $pago) {
                        DB::table('plan_pagos')->insert([
                            'cotizacion_id' => $cotizacionId,
                            'nombre' => $pago['nombre'],
                            'numero_pago' => $index + 1, // 1, 2, 3...
                            'total_pagos_plan' => $totalPagosPlan, // Total de divisiones (ej. 10)
                            'porcentaje' => $pago['porcentaje'],
                            'monto' => $pago['monto'],
                            'estatus' => 'pendiente',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Registrar interacción de Generación de Remisión
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'SE GENERO EL PDF DE REMISION')->first();
                if ($interaccion) {
                    $interaccionId = $interaccion->id ?? $interaccion->interaccion_id;
                    DB::table('proyecto_interacciones')->insert([
                        'proyecto_id' => $proyecto_id,
                        'interaccion_id' => $interaccionId,
                        'user_id' => auth()->id(),
                        'comentarios' => 'Se generó el PDF de remisión.',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $pdf = Pdf::loadView('ERP.pdf_remision', compact('proyecto', 'articulos', 'totales', 'cotizacionId', 'pagos', 'rfc', 'condiciones'));
            return $pdf->download('Remision_' . $proyecto['nombre_proyecto'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generarProduccionPdf(Request $request)
    {
        try {
            $proyecto_id = $request->input('proyecto_id');
            $articulos_ids = $request->input('articulos_ids');

            // 1. Obtener datos del proyecto
            $proyectoData = DB::table('Proyectos')
                ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
                ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
                ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
                ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
                ->leftJoin('vendedores', 'proyecto_detalles.vendedor_id', '=', 'vendedores.vendedor_id')
                ->select(
                    'Proyectos.proyecto_id',
                    'Proyectos.cliente_id',
                    'Proyectos.nombre as nombre_proyecto',
                    DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                    DB::raw("COALESCE(P1.telefono, P2.telefono) as telefono"),
                    DB::raw("COALESCE(P1.correo, P2.correo) as correo"),
                    DB::raw("COALESCE(proyecto_detalles.direccion_entrega, NULLIF(CONCAT_WS(', ', COALESCE(P1.calle, P2.calle), COALESCE(P1.municipio, P2.municipio)), '')) as direccion"),
                    'Proyectos.created_at as fecha',
                    DB::raw("CONCAT_WS(' ', vendedores.nombre, vendedores.apellido_paterno) as vendedor_nombre")
                )
                ->where('Proyectos.proyecto_id', $proyecto_id)
                ->first();

            if (!$proyectoData) {
                return response()->json(['error' => 'Proyecto no encontrado'], 404);
            }
            
            $proyecto = (array)$proyectoData;

            // 2. Obtener artículos
            $queryArticulos = DB::table('proyecto_articulos')->where('proyecto_id', $proyecto_id);
            
            if (!empty($articulos_ids) && is_array($articulos_ids)) {
                $queryArticulos->whereIn('id', $articulos_ids);
            }

            $articulos = $queryArticulos
                ->select('articulo_produccion_id as id_articulo_produccion', 'nombre', 'descripcion', 'alto', 'ancho', 'profundo', 'cantidad', 'precio as precio_unitario', 'cubicaje', 'peso', 'imagen')
                ->get()
                ->map(function($item){ return (array)$item; })
                ->toArray();
                
            if (empty($articulos)) {
                return response()->json(['error' => 'No hay artículos seleccionados para generar el documento.'], 404);
            }

            // Verificar y crear columna de control de impresión si no existe
            if (!\Illuminate\Support\Facades\Schema::hasColumn('proyecto_articulos', 'impreso_produccion')) {
                \Illuminate\Support\Facades\Schema::table('proyecto_articulos', function ($table) {
                    $table->boolean('impreso_produccion')->default(0);
                });
            }

            // Marcar los artículos como impresos
            if (!empty($articulos_ids) && is_array($articulos_ids)) {
                DB::table('proyecto_articulos')->whereIn('id', $articulos_ids)->update(['impreso_produccion' => 1]);
            }

            $qrUrl = route('seguimientoProyectos', ['proyecto_id' => $proyecto_id]);
            
            // 3. Generar el código QR a través de la API y convertirlo en base64 para embeberlo en PDF
            $qrImage = base64_encode(file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrUrl)));

            // Registrar interacción de Generación de PDF de Producción
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'SE GENERO EL PDF DEL ACTA DE PRODUCCION')->first();
                if ($interaccion) {
                    $interaccionId = $interaccion->id ?? $interaccion->interaccion_id;
                    DB::table('proyecto_interacciones')->insert([
                        'proyecto_id' => $proyecto_id,
                        'interaccion_id' => $interaccionId,
                        'user_id' => auth()->id(),
                        'comentarios' => 'Se generó el PDF del acta de producción.',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $pdf = Pdf::loadView('ERP.pdf_produccion', compact('proyecto', 'articulos', 'qrImage'));
            return $pdf->download('Produccion_' . $proyecto['nombre_proyecto'] . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function vistaProduccionProyecto($id)
    {
        $proyecto = DB::table('Proyectos')
            ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
            ->select(
                'Proyectos.proyecto_id',
                'Proyectos.nombre',
                'Proyectos.estatus',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                DB::raw("COALESCE(P1.telefono, P2.telefono) as telefono"),
                DB::raw("COALESCE(P1.correo, P2.correo) as correo"),
                DB::raw("COALESCE(proyecto_detalles.direccion_entrega, NULLIF(CONCAT_WS(', ', COALESCE(P1.calle, P2.calle), COALESCE(P1.municipio, P2.municipio)), '')) as direccion")
            )
            ->where('Proyectos.proyecto_id', $id)
            ->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $articulos = DB::table('proyecto_articulos')->where('proyecto_id', $id)->get();

        $interacciones = [];
        $historial = collect();

        if (\Illuminate\Support\Facades\Schema::hasTable('interacciones')) {
            $interacciones = DB::table('interacciones')->get();
            
            // Crear un mapa para asociar el ID de la interacción con su nombre fácilmente
            $interaccionesMap = [];
            foreach ($interacciones as $int) {
                $pk = $int->id ?? $int->interaccion_id ?? $int->nombre;
                $interaccionesMap[$pk] = $int->nombre;
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $historial = DB::table('proyecto_interacciones')
                    ->where('proyecto_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($h) use ($interaccionesMap) {
                        $h->interaccion_nombre = $interaccionesMap[$h->interaccion_id] ?? $h->interaccion_id;
                        return $h;
                    });
            }
        }

        return view('ERP.vistaProduccionProyecto', compact('proyecto', 'articulos', 'interacciones', 'historial'));
    }

    public function escanerProduccion()
    {
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        $rolesProduccionCoords = ['SUP. CARPINTERÍA', 'SUP. CARPINTERIA', 'SUP. BARNIZ Y LIJADO', 'COORD. INSTALACIÓN / SUP. HERRERÍA', 'COORD. INSTALACION / SUP. HERRERIA', 'COORD. PRODUCCIÓN/COMPRAS', 'COORD. PRODUCCION/COMPRAS'];
        $rolesProduccionScan = ['PRODUCCIÓN', 'PRODUCCION', 'LOGÍSTICA', 'LOGISTICA', 'ALMACÉN', 'ALMACEN', 'COORD. INSTALACIÓN', 'COORD. INSTALACION'];
        if (!in_array($role, array_merge(['ADMIN', 'VENDEDOR/DISEÑADOR'], $rolesProduccionCoords, $rolesProduccionScan))) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
        }

        return view('ERP.escanerProduccion');
    }

    public function guardarInteraccionProduccion(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required',
            'interaccion_id' => 'required'
        ]);

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $request->proyecto_id,
                    'interaccion_id' => $request->interaccion_id,
                    'user_id' => auth()->id(),
                    'comentarios' => $request->comentarios,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reporteEstatusProyecto()
    {
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        
        $canAccess = in_array($role, ['ADMIN', 'DIRECCIÓN', 'DIRECCION', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA', 'COORD. DV&MKT']) || ($role === 'VENDEDOR/DISEÑADOR' && strtoupper(auth()->user()->name) === 'SAUL APARICIO TORRES');

        if (!$canAccess) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
        }

        $proyectos = DB::table('Proyectos')
            ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->select(
                'Proyectos.proyecto_id',
                'Proyectos.nombre as nombre_proyecto',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                'Proyectos.estatus'
            )
            ->orderBy('Proyectos.proyecto_id', 'desc')
            ->get();

        $interacciones = DB::table('interacciones')->get();

        return view('ERP.reporteEstatusProyecto', compact('proyectos', 'interacciones'));
    }

    public function obtenerHistorialInteracciones($proyecto_id)
    {
        $interaccionesTable = DB::table('interacciones')->get();
        $interaccionesMap = [];
        foreach ($interaccionesTable as $int) {
            $pk = $int->id ?? $int->interaccion_id ?? $int->nombre;
            $interaccionesMap[$pk] = $int->nombre;
        }

        $query = DB::table('proyecto_interacciones')->where('proyecto_id', $proyecto_id);

        if (\Illuminate\Support\Facades\Schema::hasColumn('proyecto_interacciones', 'user_id')) {
            $query->leftJoin('users', 'proyecto_interacciones.user_id', '=', 'users.id')->select('proyecto_interacciones.*', 'users.name as usuario_nombre');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('proyecto_interacciones', 'usuario_id')) {
            $query->leftJoin('users', 'proyecto_interacciones.usuario_id', '=', 'users.id')->select('proyecto_interacciones.*', 'users.name as usuario_nombre');
        } else {
            $query->select('proyecto_interacciones.*');
        }

        $historial = $query->orderBy('created_at', 'desc')->get()->map(function($h) use ($interaccionesMap) {
            $h->interaccion_nombre = $interaccionesMap[$h->interaccion_id] ?? 'Interacción Desconocida';
            $h->fecha_formateada = $h->created_at ? \Carbon\Carbon::parse($h->created_at)->timezone('America/Mexico_City')->format('d/m/Y h:i A') : 'Fecha desconocida';
            
            if (!isset($h->usuario_nombre)) {
                $h->usuario_nombre = 'Sistema / No registrado';
                if (preg_match('/por (.*?)\./', $h->comentarios, $matches)) {
                    $h->usuario_nombre = trim($matches[1]);
                }
            }
            return $h;
        });

        return response()->json($historial);
    }

    public function cobranza()
    {
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        if (!in_array($role, ['ADMIN', 'COORD. DV SOLFERINO', 'COORD. DV&MKT', 'ADMINISTRACIÓN', 'ADMINISTRACION', 'DIRECCIÓN', 'DIRECCION'])) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a cobranza.');
        }

        // 1. Find the latest cotizacion_id for each project that has a payment plan.
        $latestCotizacionesConPlan = DB::table('cotizaciones as c')
            ->join('plan_pagos as pp', 'c.cotizacion_id', '=', 'pp.cotizacion_id')
            ->select('c.proyecto_id', DB::raw('MAX(c.cotizacion_id) as cotizacion_id'))
            ->groupBy('c.proyecto_id');

        // 2. Get payment summaries for all payment plans
        $paymentSummaries = DB::table('plan_pagos')
            ->select(
                'cotizacion_id',
                DB::raw('SUM(monto_pagado) as total_pagado'),
                DB::raw('SUM(monto) as total_plan')
            )
            ->groupBy('cotizacion_id');

        // 3. Now, get the full project details using these cotizacion_ids and join summaries.
        $proyectos = DB::table('Proyectos as pr')
            ->joinSub($latestCotizacionesConPlan, 'latest_c', function ($join) {
                $join->on('pr.proyecto_id', '=', 'latest_c.proyecto_id');
            })
            ->join('cotizaciones', 'latest_c.cotizacion_id', '=', 'cotizaciones.cotizacion_id')
            ->joinSub($paymentSummaries, 'summaries', function ($join) {
                $join->on('cotizaciones.cotizacion_id', '=', 'summaries.cotizacion_id');
            })
            ->leftJoin('Clientes', 'pr.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'pr.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->select(
                'pr.proyecto_id',
                'pr.nombre as nombre_proyecto',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                'cotizaciones.total as total_cotizacion',
                'cotizaciones.cotizacion_id',
                'cotizaciones.saldo_afavor',
                'summaries.total_pagado',
                'summaries.total_plan'
            )
            ->orderBy('pr.proyecto_id', 'desc')
            ->get();

        // 4. Calculate the pending balance
        foreach ($proyectos as $proyecto) {
            $proyecto->total_pagado = (float)($proyecto->total_pagado ?? 0);
            $proyecto->total_plan = (float)($proyecto->total_plan ?? 0);
            $proyecto->saldo_afavor = (float)($proyecto->saldo_afavor ?? 0);
            $proyecto->saldo_pendiente = $proyecto->total_plan - $proyecto->total_pagado;
        }

        return view('ERP.cobranza', compact('proyectos'));
    }

    public function obtenerPlanPagos($cotizacion_id)
    {
        if (!$cotizacion_id || $cotizacion_id == 'undefined') {
            return response()->json([]);
        }

        $plan = DB::table('plan_pagos')
            ->where('cotizacion_id', $cotizacion_id)
            ->orderBy('numero_pago', 'asc')
            ->get();

        return response()->json($plan);
    }

    public function registrarPago(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pago_id' => 'required|exists:plan_pagos,id',
            'monto_abono' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pagoId = $request->pago_id;
            $montoAbono = (float) $request->monto_abono;
            $fechaPago = now(); // La fecha es la actual del servidor

            // 1. Obtener el pago inicial y la cotización asociada
            $pagoInicial = DB::table('plan_pagos')->where('id', $pagoId)->first();

            if (!$pagoInicial) {
                return response()->json(['success' => false, 'message' => 'Pago no encontrado.'], 404);
            }

            // 2. Usar cualquier saldo a favor existente
            $cotizacion = DB::table('cotizaciones')->where('cotizacion_id', $pagoInicial->cotizacion_id)->first();
            $saldoAfavorExistente = (float)($cotizacion->saldo_afavor ?? 0);

            if ($saldoAfavorExistente > 0) {
                $montoAbono += $saldoAfavorExistente;
                DB::table('cotizaciones')->where('cotizacion_id', $pagoInicial->cotizacion_id)->update(['saldo_afavor' => 0]);
            }

            // 3. Obtener todos los pagos del plan ordenados
            $planCompleto = DB::table('plan_pagos')
                ->where('cotizacion_id', $pagoInicial->cotizacion_id)
                ->orderBy('numero_pago', 'asc')
                ->get();
            
            $remanente = $montoAbono;
            $procesar = false;

            // 3. Distribuir el abono en "cascada"
            foreach ($planCompleto as $pago) {
                // Empezar a procesar desde el pago seleccionado en adelante
                if ($pago->id == $pagoId) {
                    $procesar = true;
                }
                if (!$procesar) continue;
                if ($remanente <= 0) break;

                $montoPendiente = (float)$pago->monto - (float)$pago->monto_pagado;
                
                // Si el pago ya está completo, saltar al siguiente (salvo que sea el último y sobre dinero)
                if ($montoPendiente <= 0.001) continue;

                $montoAplicar = min($remanente, $montoPendiente);
                
                $nuevoPagado = (float)$pago->monto_pagado + $montoAplicar;
                $remanente -= $montoAplicar;
                
                // Determinar estatus
                $esPagadoTotalmente = abs($nuevoPagado - (float)$pago->monto) < 0.01;
                
                DB::table('plan_pagos')
                    ->where('id', $pago->id)
                    ->update([
                        'monto_pagado' => $nuevoPagado,
                        'estatus' => $esPagadoTotalmente ? 'pagado' : 'parcial',
                        'fecha_pago_real' => $fechaPago,
                        'updated_at' => now(),
                    ]);
            }

            // 5. Si aún sobra dinero (remanente), guardarlo como saldo a favor en la cotización
            if ($remanente > 0) {
                DB::table('cotizaciones')
                    ->where('cotizacion_id', $pagoInicial->cotizacion_id)
                    ->update(['saldo_afavor' => $remanente]);
            }

            // Lógica para convertir Prospecto de CT a Cliente al realizar un pago
            $datosProspecto = DB::table('cotizaciones')
                ->join('Proyectos', 'cotizaciones.proyecto_id', '=', 'Proyectos.proyecto_id')
                ->join('prospectos', 'Proyectos.prospecto_id', '=', 'prospectos.prospecto_id')
                ->leftJoin('empresas', 'prospectos.empresa_id', '=', 'empresas.empresa_id')
                ->where('cotizaciones.cotizacion_id', $pagoInicial->cotizacion_id)
                ->select('Proyectos.proyecto_id', 'Proyectos.cliente_id', 'prospectos.prospecto_id', 'empresas.nombre as empresa_nombre')
                ->first();

            if ($datosProspecto && is_null($datosProspecto->cliente_id)) {
                // Verificar si es prospecto de Casa Tapier (CT)
                if ($datosProspecto->empresa_nombre && (stripos($datosProspecto->empresa_nombre, 'Casa Tapier') !== false || $datosProspecto->empresa_nombre == 'CT')) {
                    
                    // 1. Guardar en tabla Clientes
                    $nuevoClienteId = DB::table('Clientes')->insertGetId([
                        'prospecto_id' => $datosProspecto->prospecto_id
                    ]);

                    // 2. Actualizar Proyecto
                    DB::table('Proyectos')
                        ->where('proyecto_id', $datosProspecto->proyecto_id)
                        ->update(['cliente_id' => $nuevoClienteId]);

                    // 3. Actualizar Estatus del Prospecto
                    $estatusCliente = DB::table('estatus')->where('nombre', 'Cliente')->value('estatus_id');
                    if ($estatusCliente) {
                        DB::table('prospectos')
                            ->where('prospecto_id', $datosProspecto->prospecto_id)
                            ->update([
                                'estatus_id' => $estatusCliente,
                                'updated_at' => now()
                            ]);
                    }
                }
            }

            // Registrar interacción del pago en el historial del proyecto
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones') && isset($cotizacion->proyecto_id)) {
                $interaccion = DB::table('interacciones')->where('nombre', 'PAGO DE ANTICIPO DE PROYECTO')->first();
                $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'PAGO DE ANTICIPO DE PROYECTO') : 'PAGO DE ANTICIPO DE PROYECTO';

                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $cotizacion->proyecto_id,
                    'interaccion_id' => $interaccionId,
                    'user_id' => auth()->id(),
                    'comentarios' => 'Se registró un abono de $' . number_format((float)$request->monto_abono, 2) . ' para el concepto: ' . $pagoInicial->nombre . '.',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            // Devolver el plan de pagos actualizado y el nuevo saldo a favor
            $planActualizado = DB::table('plan_pagos')
                ->where('cotizacion_id', $pagoInicial->cotizacion_id)
                ->orderBy('numero_pago', 'asc')
                ->get();
            
            $cotizacionActualizada = DB::table('cotizaciones')->where('cotizacion_id', $pagoInicial->cotizacion_id)->first();

            return response()->json(['success' => true, 'message' => 'Pago registrado correctamente.', 'plan' => $planActualizado, 'saldo_afavor' => (float)($cotizacionActualizada->saldo_afavor ?? 0)]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()], 500);
        }
    }

          public function altaEstatus()
      {
          // Validar que el usuario en sesión tenga el rol autorizado
          $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
          if (!in_array($userRoleName, ['VENDEDOR/DISEÑADOR', 'ADMIN'])) {
              return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista. Solo los vendedores/diseñadores y administradores pueden ingresar.');
          }

          $proyectos = DB::table('Proyectos')
              ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
              ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
              ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
              ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
              ->select(
                  'Proyectos.proyecto_id',
                  'Proyectos.nombre as nombre_proyecto',
                  DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                  DB::raw('COALESCE(proyecto_detalles.es_planta_baja, 0) as es_planta_baja'),
                  DB::raw('COALESCE(proyecto_detalles.condiciones_acceso, "") as condiciones_acceso'),
                  DB::raw('COALESCE(proyecto_detalles.requiere_instalacion, 0) as requiere_instalacion'),
                  DB::raw('COALESCE(proyecto_detalles.requiere_desemplaye, 0) as requiere_desemplaye'),
                  DB::raw('COALESCE(proyecto_detalles.requiere_emplaye, 0) as requiere_emplaye'),
                  DB::raw('COALESCE(proyecto_detalles.requiere_maniobraje, 0) as requiere_maniobraje')
              )
              ->orderBy('Proyectos.proyecto_id', 'desc')
              ->get();

          // Extraer interacciones y filtrar solo de la 2 a la 7
          $interacciones = collect();
          if (\Illuminate\Support\Facades\Schema::hasTable('interacciones')) {
              $interacciones = DB::table('interacciones')->get()->filter(function($int) {
                  $id = $int->id ?? $int->interaccion_id;
                  return (($id >= 2 && $id <= 7) || ($id >= 10 && $id <= 13));
              })->values();
          }

          return view('ERP.altaEstatus', compact('proyectos', 'interacciones'));
      }

      public function guardarAltaEstatus(Request $request)
      {
          $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
          if (!in_array($userRoleName, ['VENDEDOR/DISEÑADOR', 'ADMIN'])) {
              return response()->json(['success' => false, 'message' => 'No estás autorizado para realizar esta acción.'], 403);
          }

          $request->validate([
              'proyecto_id' => 'required',
              'interaccion_id' => 'required'
          ]);

          try {
              if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                  DB::table('proyecto_interacciones')->insert([
                      'proyecto_id' => $request->proyecto_id,
                      'interaccion_id' => $request->interaccion_id,
                      'user_id' => auth()->id(),
                      'comentarios' => $request->comentarios,
                      'created_at' => now(),
                      'updated_at' => now()
                  ]);
              }

              return response()->json(['success' => true]);
          } catch (\Exception $e) {
              return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
          }
      }

    public function guardarCotizacion(Request $request)
    {
        try {
            $data = $request->all();
            $proyecto = $data['proyecto'];
            $articulos = $data['articulos'];
            $totales = $data['totales'];

            // Actualizar porcentajes de IVA y Descuento en el Prospecto asociado
            $prospectoId = $proyecto['prospecto_id'] ?? null;
            
            // Si no hay prospecto_id directo en el proyecto, buscar a través del cliente
            if (!$prospectoId && !empty($proyecto['cliente_id'])) {
                $cliente = DB::table('Clientes')->where('cliente_id', $proyecto['cliente_id'])->first();
                if ($cliente) {
                    $prospectoId = $cliente->prospecto_id;
                }
            }

            if ($prospectoId) {
                DB::table('prospectos')
                    ->where('prospecto_id', $prospectoId)
                    ->update([
                        'iva' => $totales['iva_porcentaje'] ?? 0,
                        'tiene_iva' => ($totales['iva_porcentaje'] ?? 0) > 0 ? 1 : 0,
                        'descuento' => $totales['descuento_porcentaje'] ?? 0,
                        'tiene_descuento' => ($totales['descuento_porcentaje'] ?? 0) > 0 ? 1 : 0
                    ]);
            }

            // Asegurar que la tabla soporte la columna 'autorizado'
            if (!\Illuminate\Support\Facades\Schema::hasColumn('cotizaciones', 'autorizado')) {
                \Illuminate\Support\Facades\Schema::table('cotizaciones', function ($table) {
                    $table->boolean('autorizado')->default(0);
                });
            }

            // 1. Guardar precio de cada artículo (acepta 0 si no se ha llenado)
            foreach ($articulos as $art) {
                DB::table('proyecto_articulos')
                    ->where('id', $art['id'])
                    ->update(['precio' => (float)($art['precio_unitario'] ?? 0)]);
            }

            // 2. Guardar datos generales en cotizaciones
            $cotizacionId = DB::table('cotizaciones')->insertGetId([
                'proyecto_id' => $proyecto['proyecto_id'],
                'subtotal' => $totales['subtotal'] ?? 0,
                'envio' => (float)($totales['envio'] ?? 0),
                'descuento' => $totales['descuento'] ?? 0,
                'iva' => $totales['iva'] ?? 0,
                'total' => $totales['total'] ?? 0,
                'autorizado' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Guardado correctamente', 'cotizacion_id' => $cotizacionId]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function obtenerCotizacion($proyecto_id)
    {
        $cotizacion = DB::table('cotizaciones')
            ->where('proyecto_id', $proyecto_id)
            ->orderBy('cotizacion_id', 'desc')
            ->first();

        return response()->json($cotizacion);
    }

    public function autorizarCotizacionInterna(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required'
        ]);

        try {
            $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
            if (!in_array($userRoleName, ['ADMIN', 'COORD. DV&MKT', 'COORD. DV SOLFERINO', 'VENDEDOR/DISEÑADOR'])) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
            }

            // Asegurar que la tabla soporte la columna 'autorizado'
            if (!\Illuminate\Support\Facades\Schema::hasColumn('cotizaciones', 'autorizado')) {
                \Illuminate\Support\Facades\Schema::table('cotizaciones', function ($table) {
                    $table->boolean('autorizado')->default(0);
                });
            }

            // Autorizar la última cotización guardada de este proyecto
            DB::table('cotizaciones')
                ->where('proyecto_id', $request->proyecto_id)
                ->orderBy('cotizacion_id', 'desc')
                ->limit(1)
                ->update(['autorizado' => 1]);

            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'AUTORIZACION INTERNA DE COTIZACION')->first();
                $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'AUTORIZACION INTERNA DE COTIZACION') : 'AUTORIZACION INTERNA DE COTIZACION';

                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $request->proyecto_id,
                    'interaccion_id' => $interaccionId,
                    'user_id' => auth()->id(),
                    'comentarios' => 'La cotización ha sido autorizada internamente por ' . auth()->user()->name . '.',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function ajustarCotizacionInterna(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required'
        ]);

        try {
            // Desbloquear (quitar autorización) a la última cotización
            DB::table('cotizaciones')
                ->where('proyecto_id', $request->proyecto_id)
                ->orderBy('cotizacion_id', 'desc')
                ->limit(1)
                ->update(['autorizado' => 0]);

            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'AJUSTE DE COTIZACION')->first();
                $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'AJUSTE DE COTIZACION') : 'AJUSTE DE COTIZACION';

                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $request->proyecto_id,
                    'interaccion_id' => $interaccionId,
                    'user_id' => auth()->id(),
                    'comentarios' => 'Se ha solicitado un ajuste a la cotización por ' . auth()->user()->name . '. La cotización requerirá ser autorizada nuevamente tras ser guardada.',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logistica()
    {
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        if (!in_array($role, ['ADMIN', 'COORD. LOGÍSTICA', 'COORD. LOGISTICA'])) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a logística.');
        }

        $proyectos = DB::table('Proyectos')
            ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
            ->leftJoin('vendedores', 'proyecto_detalles.vendedor_id', '=', 'vendedores.vendedor_id')
            ->select(
                'Proyectos.proyecto_id as id',
                'Proyectos.nombre',
                'Proyectos.estatus',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno), '')) as cliente"),
                DB::raw("COALESCE(proyecto_detalles.direccion_entrega, NULLIF(CONCAT_WS(', ', COALESCE(P1.calle, P2.calle), COALESCE(P1.municipio, P2.municipio)), '')) as direccion"),
                DB::raw("CONCAT_WS(' ', vendedores.nombre, vendedores.apellido_paterno) as vendedor_nombre"),
                DB::raw('COALESCE(proyecto_detalles.es_planta_baja, 0) as es_planta_baja'),
                DB::raw('COALESCE(proyecto_detalles.condiciones_acceso, "") as condiciones_acceso'),
                DB::raw('COALESCE(proyecto_detalles.requiere_instalacion, 0) as requiere_instalacion'),
                DB::raw('COALESCE(proyecto_detalles.requiere_desemplaye, 0) as requiere_desemplaye'),
                DB::raw('COALESCE(proyecto_detalles.requiere_emplaye, 0) as requiere_emplaye'),
                DB::raw('COALESCE(proyecto_detalles.requiere_maniobraje, 0) as requiere_maniobraje')
            )
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('proyecto_articulos')
                      ->whereColumn('proyecto_articulos.proyecto_id', 'Proyectos.proyecto_id');
            })
            ->orderBy('Proyectos.proyecto_id', 'desc')
            ->get();

        // Traer artículos de los proyectos vigentes
        $projectIds = $proyectos->pluck('id');
        $articulos = DB::table('proyecto_articulos')
            ->whereIn('proyecto_id', $projectIds)
            ->select('id', 'proyecto_id', 'nombre', 'descripcion', 'alto', 'ancho', 'profundo', 'peso', 'cubicaje', 'cantidad', 'imagen')
            ->get()
            ->map(function ($art) {
                if ($art->imagen) {
                    $art->imagen = asset('storage/' . $art->imagen);
                }
                return $art;
            });

        $retornos = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('retornos')) {
            $retornos = DB::table('retornos')
                ->leftJoin('proyecto_articulos', 'retornos.articulo_id', '=', 'proyecto_articulos.id')
                ->select(
                    'retornos.*',
                    'proyecto_articulos.nombre as articulo_nombre'
                )
                ->orderBy('retornos.created_at', 'desc')
                ->get()
                ->map(function($r) {
                    $r->fecha_formateada = $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '';
                    return $r;
                });
        }

        return view('ERP.logistica', compact('proyectos', 'articulos', 'retornos'));
    }

    public function guardarLogisticaProyecto(Request $request)
    {
        try {
            DB::table('proyecto_detalles')->updateOrInsert(
                ['detalles_id' => $request->proyecto_id],
                [ // Convert 'si'/'no' to 1/0 for the database
                    'es_planta_baja' => ($request->es_planta_baja === 'si') ? 1 : 0,
                    'condiciones_acceso' => $request->condiciones_acceso,
                    'requiere_instalacion' => $request->requiere_instalacion ? 1 : 0,
                    'requiere_desemplaye' => $request->requiere_desemplaye ? 1 : 0,
                    'requiere_emplaye' => $request->requiere_emplaye ? 1 : 0,
                    'requiere_maniobraje' => $request->requiere_maniobraje ? 1 : 0,
                ]
            );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarRetorno(Request $request)
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('retornos')) {
                DB::table('retornos')->insert([
                    'proyecto_id' => $request->proyecto_id,
                    'articulo_id' => $request->articulo_id,
                    'destinatario' => $request->destinatario,
                    'ubicacion_interna' => $request->ubicacion_interna,
                    'persona_logistica' => $request->persona_logistica,
                    'estatus' => 'En Revisión',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Registrar interacción de retorno en el historial general del proyecto
            if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                $interaccion = DB::table('interacciones')->where('nombre', 'REPORTE DE RETORNO')->first();
                $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'REPORTE DE RETORNO') : 'REPORTE DE RETORNO';

                DB::table('proyecto_interacciones')->insert([
                    'proyecto_id' => $request->proyecto_id,
                    'interaccion_id' => $interaccionId,
                    'user_id' => auth()->id(),
                    'comentarios' => "Se ha generado un reporte de retorno.\nArtículo: " . $request->articulo_nombre . "\nDestinatario: " . $request->destinatario . "\nUbicación: " . $request->ubicacion_interna . "\nPersona a cargo: " . $request->persona_logistica,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

      public function obtenerProyectosCliente($id)
      {
          try {
              $proyectos = DB::table('Proyectos')
                  ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
                  ->leftJoin('empresas', 'proyecto_detalles.empresa_id', '=', 'empresas.empresa_id')
                  ->where('Proyectos.cliente_id', $id)
                  ->select(
                      'Proyectos.proyecto_id',
                      'Proyectos.nombre',
                      'Proyectos.estatus',
                      'proyecto_detalles.descripcion',
                      'proyecto_detalles.maps',
                      'empresas.nombre as empresa'
                  )
                  ->orderBy('Proyectos.proyecto_id', 'desc')
                  ->get();

              return response()->json($proyectos);
          } catch (\Exception $e) {
              return response()->json(['error' => $e->getMessage()], 500);
          }
      }

      public function altasCategorias()
      {
          // Obtenemos las categorías para llenar el select inicial
          $categorias = DB::table('categorias')->orderBy('nombre', 'asc')->get();
          return view('ERP.altasCategorias', compact('categorias'));
      }

      public function guardarCategoria(Request $request)
      {
          $request->validate([
              'nombre' => 'required|string|max:100|unique:categorias,nombre',
          ]);

          try {
              $id = DB::table('categorias')->insertGetId([
                  'nombre' => $request->nombre,
              ]);

              // Si la petición viene de AJAX (AlpineJS), devolvemos JSON
              if ($request->ajax()) {
                  return response()->json([
                      'success' => true,
                      'categoria' => ['categoria_id' => $id, 'nombre' => $request->nombre],
                      'mensaje' => 'Categoría creada correctamente'
                  ]);
              }

              return redirect()->back()->with('mensaje', 'Categoría creada correctamente');
          } catch (\Exception $e) {
              if ($request->ajax()) {
                  return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
              }
              return redirect()->back()->with('error', 'Error al crear categoría');
          }
      }

      public function guardarArticulo(Request $request)
      {
          $request->validate([
              'sku' => 'nullable|string|max:50',
              'nombre' => 'required|string|max:150',
              'categoria_id' => 'required|exists:categorias,categoria_id',
              'precio' => 'required|numeric',
              'peso' => 'nullable|numeric',
              'dimensiones' => 'nullable|string|max:100',
              'descripcion' => 'nullable|string',
              'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
              'pdf_archivo' => 'nullable|mimes:pdf|max:10000'
          ]);

          $data = $request->only([
              'sku', 
              'nombre', 
              'categoria_id', 
              'precio', 
              'peso', 
              'dimensiones', 
              'descripcion'
          ]);

          if ($request->hasFile('foto')) {
              $file = $request->file('foto');
              $filename = time() . '_' . $file->getClientOriginalName();
              FacadeStorage::disk('public')->put($filename, \File::get($file));
              $data['imagen'] = $filename; // Guardamos el nombre del archivo en la columna 'imagen'
          }

          if ($request->hasFile('pdf_archivo')) {
              $file = $request->file('pdf_archivo');
              $filename = time() . '_pdf_' . $file->getClientOriginalName();
              FacadeStorage::disk('public')->put($filename, \File::get($file));
              $data['pdf_archivo'] = $filename;
          }

          DB::table('articulos')->insert($data);

          return redirect()->back()->with('mensaje', 'Artículo guardado correctamente');
      }

      public function guardarArticulosProduccion(Request $request)
      {
          $request->validate([
              'proyecto_id' => 'required|exists:Proyectos,proyecto_id',
              'articulos' => 'nullable|array'
          ]);

          DB::beginTransaction();
          try {
              $proyecto_id = $request->proyecto_id;
              $incoming_ids = []; // Para rastrear qué IDs se mantienen/actualizan

              $articulos = $request->articulos ?? [];

              foreach ($articulos as $index => $item) {
                  $data = [
                      'proyecto_id' => $request->proyecto_id,
                      'articulo_produccion_id' => $item['id_articulo_produccion'] ?? null,
                      'categoria_id' => $item['categoria_articulo_id'] ?? null,
                      'nombre' => $item['nombre'],
                      'descripcion' => $item['descripcion'] ?? null,
                      'alto' => $item['alto'] ?? 0,
                      'ancho' => $item['ancho'] ?? 0,
                      'profundo' => $item['profundo'] ?? 0,
                      'peso' => $item['peso'] ?? 0,
                      'cubicaje' => $item['cubicaje'] ?? 0,
                      'cantidad' => $item['cantidad'] ?? 1,
                      'tiene_division' => $item['tiene_division'] ?? 0,
                      'piezas_divididas' => $item['piezas_divididas'] ?? 0,
                      'created_at' => now(),
                      'updated_at' => now()
                  ];

                  // Manejo de Imagen Base64 (viene del preview de JS)
                  if (isset($item['imagen_base64']) && preg_match('/^data:image\/(\w+);base64,/', $item['imagen_base64'], $type)) {
                      $data['imagen'] = 'prod_' . time() . '_' . uniqid() . '.' . strtolower($type[1]);
                      $image_base64 = substr($item['imagen_base64'], strpos($item['imagen_base64'], ',') + 1);
                      $image_base64 = base64_decode($image_base64);
                      FacadeStorage::disk('public')->put($data['imagen'], $image_base64);
                  } elseif (isset($item['imagen_ruta']) && !empty($item['imagen_ruta'])) {
                      // Mantiene la imagen existente al duplicar o editar sin cambios
                      $data['imagen'] = $item['imagen_ruta'];
                  }

                  // Manejo de PDF (Archivo real enviado por FormData)
                  if ($request->hasFile("articulos.$index.pdf_archivo")) {
                      $file = $request->file("articulos.$index.pdf_archivo");
                      $filename = 'pdf_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                      FacadeStorage::disk('public')->put($filename, \File::get($file));
                      $data['pdf_archivo'] = $filename;
                  }

                  // Lógica de Sincronización (Update vs Insert)
                  $id = $item['id'] ?? null;

                  if ($id) {
                      // Actualizar existente
                      DB::table('proyecto_articulos')->where('id', $id)->update($data);
                      $proyectoArticuloId = $id;
                      $incoming_ids[] = $id;
                      
                      // Borramos materiales viejos para reinsertarlos limpios
                      DB::table('proyecto_articulo_materiales')->where('proyecto_articulo_id', $id)->delete();
                  } else {
                      // Insertar nuevo
                      $data['created_at'] = now();
                      $proyectoArticuloId = DB::table('proyecto_articulos')->insertGetId($data);
                      $incoming_ids[] = $proyectoArticuloId;
                  }

                  // Guardar Materiales relacionados
                  if (isset($item['materiales']) && is_array($item['materiales'])) {
                    foreach ($item['materiales'] as $mat) {
                        DB::table('proyecto_articulo_materiales')->insert([
                            'proyecto_articulo_id' => $proyectoArticuloId,
                            'tipo_material' => $mat['tipo'] ?? 'Otro',
                            'material_id' => $mat['material_id'] ?? null,
                            'descripcion' => $mat['descripcion'] ?? '' // Para casos especiales como Herrería
                        ]);
                    }
                  }
              }

              // Eliminar artículos que estaban en la BD pero ya no vienen en la solicitud (fueron borrados en el frontend)
              DB::table('proyecto_articulos')
                  ->where('proyecto_id', $proyecto_id)
                  ->whereNotIn('id', $incoming_ids)
                  ->delete();

              // Registrar interacción de Artículos Registrados
              if (\Illuminate\Support\Facades\Schema::hasTable('proyecto_interacciones')) {
                  $interaccion = DB::table('interacciones')->where('nombre', 'ARTÍCULOS REGISTRADOS')->first();
                  $interaccionId = $interaccion ? ($interaccion->id ?? $interaccion->interaccion_id ?? 'ARTÍCULOS REGISTRADOS') : 'ARTÍCULOS REGISTRADOS';

                  $numArticulos = is_array($articulos) ? count($articulos) : 0;
                  
                  DB::table('proyecto_interacciones')->insert([
                      'proyecto_id' => $proyecto_id,
                      'interaccion_id' => $interaccionId,
                      'user_id' => auth()->id(),
                      'comentarios' => 'Se registraron/actualizaron ' . $numArticulos . ' artículo(s) de producción.',
                      'created_at' => now(),
                      'updated_at' => now()
                  ]);
              }

              DB::commit();
              return response()->json(['success' => true, 'message' => 'Artículos guardados correctamente']);
          } catch (\Exception $e) {
              DB::rollBack();
              return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
          }
      }

      public function obtenerFallasArticulo($id)
      {
          try {
              $usuariosMap = DB::table('users')->pluck('name', 'id')->toArray();

              $fallas = DB::table('fallas')
                  ->leftJoin('categorias_fallas', 'fallas.categoria_id', '=', 'categorias_fallas.id')
                  ->leftJoin('subcategoria_fallas', 'fallas.subcategoria_id', '=', 'subcategoria_fallas.id')
                  ->where('fallas.articulo_id', $id)
                  ->select(
                      'fallas.*',
                      'categorias_fallas.nombre as categoria_nombre',
                      'subcategoria_fallas.nombre as subcategoria_nombre'
                  )
                  ->orderBy('fallas.created_at', 'desc')
                  ->get()
                  ->map(function ($falla) use ($usuariosMap) {
                      $falla->fecha_hora_formateada = $falla->created_at 
                          ? \Carbon\Carbon::parse($falla->created_at)->timezone('America/Mexico_City')->format('d/m/Y h:i A') 
                          : $falla->fecha;

                      $falla->materiales_lista = json_decode($falla->materiales, true) ?? [];
                      
                      $originoIds = json_decode($falla->origino, true);
                      $originoIds = is_array($originoIds) ? $originoIds : [];
                      $falla->origino_nombres = array_map(function($id) use ($usuariosMap) {
                          return $usuariosMap[$id] ?? 'Desconocido';
                      }, $originoIds);

                      $resolvioIds = json_decode($falla->resolvio, true);
                      $resolvioIds = is_array($resolvioIds) ? $resolvioIds : [];
                      $falla->resolvio_nombres = array_map(function($id) use ($usuariosMap) {
                          return $usuariosMap[$id] ?? 'Desconocido';
                      }, $resolvioIds);

                      $falla->registrado_por_nombre = $usuariosMap[$falla->registrado_por] ?? 'Desconocido';

                      return $falla;
                  });

              return response()->json($fallas);
          } catch (\Exception $e) {
              return response()->json(['error' => $e->getMessage()], 500);
          }
      }
      public function guardarNuevaChapa(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'nombre' => 'required|string|max:255|unique:chapas,nombre',
          ]);
  
          if ($validator->fails()) {
              return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
          }
  
          $id = DB::table('chapas')->insertGetId(['nombre' => $request->nombre]);
          $chapa = DB::table('chapas')->where('chapa_id', $id)->first();
  
          return response()->json(['success' => true, 'chapa' => $chapa]);
      }
  
      public function guardarNuevoProveedor(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'nombre' => 'required|string|max:255|unique:proveedores,nombre',
          ]);
  
          if ($validator->fails()) {
              return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
          }
  
          $id = DB::table('proveedores')->insertGetId(['nombre' => $request->nombre]);
          $proveedor = DB::table('proveedores')->where('proveedor_id', $id)->first();
  
          return response()->json(['success' => true, 'proveedor' => $proveedor]);
      }
  
      public function guardarNuevoSubmaterial(Request $request)
      {
          $validator = Validator::make($request->all(), [
              'nombre' => 'required|string|max:255|unique:submateriales,nombre',
          ]);
  
          if ($validator->fails()) {
              return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
          }
  
          $id = DB::table('submateriales')->insertGetId(['nombre' => $request->nombre]);
          $submaterial = DB::table('submateriales')->where('submaterial_id', $id)->first();
  
          return response()->json(['success' => true, 'submaterial' => $submaterial]);
      }

      public function guardarNuevoMaterial(Request $request)
      {
          $tipo_map = [
              'madera' => 1,
              'melamina' => 2,
              'tela' => 3,
              'cubierta' => 4,
          ];
  
          $tipo = $request->input('tipo_material');
          $categoria_id = $tipo_map[$tipo] ?? null;
  
          if (!$categoria_id) {
              return response()->json(['success' => false, 'message' => 'Tipo de material no válido'], 400);
          }
  
          $rules = [
              'nombre' => 'required|string|max:255',
              'imagen' => 'nullable|image|max:2048',
              'tipo_material' => 'required|in:madera,melamina,tela,cubierta',
          ];
  
          $data = [
              'categoria_id' => $categoria_id,
              'nombre' => $request->nombre,
          ];
  
          if ($tipo === 'madera') {
              $rules['chapa_id'] = 'required|exists:chapas,chapa_id';
              $rules['color'] = 'required|string|max:100';
              $data['chapa_id'] = $request->chapa_id;
              $data['color'] = $request->color;
          } elseif ($tipo === 'melamina') {
              $rules['proveedor_id'] = 'required|exists:proveedores,proveedor_id';
              $rules['color'] = 'required|string|max:100';
              $rules['dibujo'] = 'required|string|max:100';
              $data['proveedor_id'] = $request->proveedor_id;
              $data['color'] = $request->color;
              $data['dibujo'] = $request->dibujo;
          } elseif ($tipo === 'tela') {
              $rules['proveedor_id'] = 'required|exists:proveedores,proveedor_id';
              $rules['submaterial_id'] = 'nullable|exists:submateriales,submaterial_id'; // Coleccion
              $rules['dibujo'] = 'required|string|max:100';
              $rules['color'] = 'required|string|max:100';
              $data['proveedor_id'] = $request->proveedor_id;
              $data['submaterial_id'] = $request->submaterial_id;
              $data['dibujo'] = $request->dibujo;
              $data['color'] = $request->color;
          } elseif ($tipo === 'cubierta') {
              $rules['submaterial_id'] = 'required|exists:submateriales,submaterial_id';
              $data['submaterial_id'] = $request->submaterial_id;
          }
  
          $validator = Validator::make($request->all(), $rules);
  
          if ($validator->fails()) {
              return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
          }
  
          if ($request->hasFile('imagen')) {
              $file = $request->file('imagen');
              $filename = 'mat_' . time() . '_' . $file->getClientOriginalName();
              FacadeStorage::disk('public')->put($filename, \File::get($file));
              $data['imagen'] = $filename;
          }
  
          $id = DB::table('materiales')->insertGetId($data);
          $material = DB::table('materiales')->where('material_id', $id)->first();

          return response()->json(['success' => true, 'material' => $material]);
      }

      public function detalleProyecto($id)
      {
          // Obtener información del proyecto y cliente
          $proyecto = DB::table('Proyectos')
              ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
              ->leftJoin('Prospectos', 'Clientes.prospecto_id', '=', 'Prospectos.prospecto_id')
              ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
              ->select(
                  'Proyectos.proyecto_id',
                  'Proyectos.nombre',
                  'Proyectos.estatus',
                  DB::raw("CONCAT(COALESCE(Prospectos.nombre,''), ' ', COALESCE(Prospectos.apellido_paterno,''), ' ', COALESCE(Prospectos.apellido_materno,'')) as cliente_nombre"),
                  
                  // Fetch project-level logistics details (Note: comma added here)
                  DB::raw('COALESCE(proyecto_detalles.es_planta_baja, 0) as es_planta_baja'),
                  DB::raw('COALESCE(proyecto_detalles.condiciones_acceso, "") as condiciones_acceso')
              )
              ->where('Proyectos.proyecto_id', $id)
              ->first();


          if (!$proyecto) {
              return redirect()->back()->with('error', 'Proyecto no encontrado');
          }

          // Obtener artículos del proyecto
          $articulos = DB::table('proyecto_articulos')
              ->where('proyecto_id', $id)
              ->get();

          // Obtener materiales para cada artículo
          foreach ($articulos as $articulo) {
              $articulo->materiales = DB::table('proyecto_articulo_materiales as pam')
                  ->leftJoin('materiales as m', 'pam.material_id', '=', 'm.material_id')
                  ->where('pam.proyecto_articulo_id', $articulo->id)
                  ->select(
                      'pam.tipo_material',
                      'pam.descripcion as custom_descripcion',
                      'm.nombre as material_nombre',
                      'm.color as material_color',
                      'm.imagen as material_imagen'
                  )
                  ->get();
          }

          return view('ERP.detalleProyecto', compact('proyecto', 'articulos'));
      }


      public function obtenerArticulosProyecto($id)
      {
          // Verificar y crear columna de control de impresión si no existe para la lectura actual
          if (!\Illuminate\Support\Facades\Schema::hasColumn('proyecto_articulos', 'impreso_produccion')) {
              \Illuminate\Support\Facades\Schema::table('proyecto_articulos', function ($table) {
                  $table->boolean('impreso_produccion')->default(0);
              });
          }

          $articulos = DB::table('proyecto_articulos')->where('proyecto_id', $id)->get();
          
          // Optimización: Cargar todos los materiales de estos artículos en una sola consulta
          $articuloIds = $articulos->pluck('id')->toArray();
          $todosMateriales = DB::table('proyecto_articulo_materiales')
              ->whereIn('proyecto_articulo_id', $articuloIds)
              ->get()
              ->groupBy('proyecto_articulo_id');
          
          // Optimización: Cargar todos los datos de materiales necesarios en una sola consulta
          $allMaterialIds = $todosMateriales->flatten()->pluck('material_id')->unique()->filter()->values();
          $materialsData = DB::table('materiales as m')
              ->leftJoin('chapas as c', 'm.chapa_id', '=', 'c.chapa_id')
              ->leftJoin('proveedores as p', 'm.proveedor_id', '=', 'p.proveedor_id')
              ->leftJoin('submateriales as s', 'm.submaterial_id', '=', 's.submaterial_id')
              ->whereIn('m.material_id', $allMaterialIds)
              ->select(
                  'm.material_id', 'm.categoria_id', 'm.nombre as material_nombre', 'm.color', 'm.dibujo',
                  'c.nombre as chapa_nombre',
                  'p.nombre as proveedor_nombre',
                  's.nombre as submaterial_nombre'
              )
              ->get()->keyBy('material_id');

          foreach ($articulos as $art) {
              // Obtener materiales específicos de este artículo desde la colección agrupada
              $materiales = $todosMateriales->get($art->id, collect());
                  
              // Reconstruir arrays de objetos {id, text} para el frontend
              $art->maderas_seleccionadas = [];
              $art->melaminas_seleccionadas = [];
              $art->telas_seleccionadas = [];
              $art->cubiertas_seleccionadas = [];

              foreach ($materiales->where('tipo_material', '!=', 'Otros') as $matRel) {
                  if (isset($matRel->material_id) && $materialsData->has($matRel->material_id)) {
                      $materialInfo = $materialsData->get($matRel->material_id);
                      $parts = [$materialInfo->material_nombre];

                      if ($materialInfo->categoria_id == 1) { // Madera
                          if($materialInfo->chapa_nombre) $parts[] = $materialInfo->chapa_nombre;
                          if($materialInfo->color) $parts[] = $materialInfo->color;
                          $art->maderas_seleccionadas[] = ['id' => $materialInfo->material_id, 'text' => implode(' - ', $parts)];
                      } elseif ($materialInfo->categoria_id == 2) { // Melamina
                          if($materialInfo->proveedor_nombre) $parts[] = $materialInfo->proveedor_nombre;
                          if($materialInfo->color) $parts[] = $materialInfo->color;
                          if($materialInfo->dibujo) $parts[] = $materialInfo->dibujo;
                          $art->melaminas_seleccionadas[] = ['id' => $materialInfo->material_id, 'text' => implode(' - ', $parts)];
                      } elseif ($materialInfo->categoria_id == 3) { // Tela
                          if($materialInfo->proveedor_nombre) $parts[] = $materialInfo->proveedor_nombre;
                          if($materialInfo->submaterial_nombre) $parts[] = $materialInfo->submaterial_nombre;
                          if($materialInfo->dibujo) $parts[] = $materialInfo->dibujo;
                          if($materialInfo->color) $parts[] = $materialInfo->color;
                          $art->telas_seleccionadas[] = ['id' => $materialInfo->material_id, 'text' => implode(' - ', $parts)];
                      } elseif ($materialInfo->categoria_id == 4) { // Cubierta
                          if($materialInfo->submaterial_nombre) $parts[] = $materialInfo->submaterial_nombre;
                          $art->cubiertas_seleccionadas[] = ['id' => $materialInfo->material_id, 'text' => implode(' - ', $parts)];
                      }
                  }
              }
              
              // Booleanos para UI
              $art->usa_madera = count($art->maderas_seleccionadas) > 0;
              $art->usa_melamina = count($art->melaminas_seleccionadas) > 0;
              $art->usa_textil = count($art->telas_seleccionadas) > 0;
              $art->usa_cubierta = count($art->cubiertas_seleccionadas) > 0;
              $art->usa_herreria = $materiales->where('tipo_material', 'Otros')->where('descripcion', 'Herrería')->count() > 0;
              
              // Mapeo de campos de BD a nombres esperados por el frontend
              $art->categoria_articulo_id = $art->categoria_id;
              $art->id_articulo_produccion = $art->articulo_produccion_id;

              // Casting explícito de campos booleanos/checkbox y mapeo de PDF
              $art->tiene_division = (bool) $art->tiene_division;
              $art->pdf = $art->pdf_archivo;

              // Ajustar ruta de imagen para visualización
              if ($art->imagen) {
                  // Guardamos la ruta relativa original para operaciones de guardado/duplicado
                  $art->imagen_ruta = $art->imagen;
                  // Guardamos la URL completa en una propiedad temporal para el frontend
                  $art->imagen = asset('storage/' . $art->imagen);
              }
          }
          
          return response()->json($articulos);
      }

    public function imprimirHistorialProyecto($proyecto_id)
    {
        $proyecto = DB::table('Proyectos')
            ->leftJoin('Clientes', 'Proyectos.cliente_id', '=', 'Clientes.cliente_id')
            ->leftJoin('Prospectos as P1', 'Proyectos.prospecto_id', '=', 'P1.prospecto_id')
            ->leftJoin('Prospectos as P2', 'Clientes.prospecto_id', '=', 'P2.prospecto_id')
            ->select(
                'Proyectos.proyecto_id',
                'Proyectos.nombre as nombre_proyecto',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', P1.nombre, P1.apellido_paterno, P1.apellido_materno), ''), NULLIF(CONCAT_WS(' ', P2.nombre, P2.apellido_paterno, P2.apellido_materno), '')) as cliente_nombre"),
                'Proyectos.estatus'
            )
            ->where('Proyectos.proyecto_id', $proyecto_id)
            ->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        // Mapear interacciones
        $interaccionesTable = DB::table('interacciones')->get();
        $interaccionesMap = [];
        foreach ($interaccionesTable as $int) {
            $pk = $int->id ?? $int->interaccion_id ?? $int->nombre;
            $interaccionesMap[$pk] = $int->nombre;
        }

        // Construcción segura del query para evitar errores si no existe un id de usuario
        $query = DB::table('proyecto_interacciones')->where('proyecto_id', $proyecto_id);

        if (\Illuminate\Support\Facades\Schema::hasColumn('proyecto_interacciones', 'user_id')) {
            $query->leftJoin('users', 'proyecto_interacciones.user_id', '=', 'users.id')->select('proyecto_interacciones.*', 'users.name as usuario_nombre');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('proyecto_interacciones', 'usuario_id')) {
            $query->leftJoin('users', 'proyecto_interacciones.usuario_id', '=', 'users.id')->select('proyecto_interacciones.*', 'users.name as usuario_nombre');
        } else {
            $query->select('proyecto_interacciones.*');
        }

        $historial = $query->orderBy('created_at', 'desc')->get()->map(function($h) use ($interaccionesMap) {
            $h->interaccion_nombre = $interaccionesMap[$h->interaccion_id] ?? 'Interacción Desconocida';
            $h->fecha_formateada = $h->created_at ? \Carbon\Carbon::parse($h->created_at)->timezone('America/Mexico_City')->format('d/m/Y H:i') : 'Fecha desconocida';
            
            if (!isset($h->usuario_nombre)) {
                $h->usuario_nombre = 'Sistema / No registrado';
                // Fallback: intentar extraer el nombre del usuario desde los comentarios si se guardó ahí (ej. "por Juan Pérez.")
                if (preg_match('/por (.*?)\./', $h->comentarios, $matches)) {
                    $h->usuario_nombre = trim($matches[1]);
                }
            }
            return $h;
        });

        $pdf = Pdf::loadView('ERP.pdf_historial_proyecto', compact('proyecto', 'historial'));
        return $pdf->download('Historial_Interacciones_' . preg_replace('/[^A-Za-z0-9]/', '_', $proyecto->nombre_proyecto) . '.pdf');
    }

    public function costosFallas()
    {
        // Role check
        $userRoleName = DB::table('roles')->where('id', auth()->user()->role)->value('nombre') ?? auth()->user()->role;
        $role = strtoupper($userRoleName);
        if (!in_array($role, ['ADMIN'])) {
            return redirect()->route('inicio')->with('error', 'No tienes permiso para acceder a esta vista.');
        }

        $fallas = DB::table('fallas as f')
            ->join('Proyectos as p', 'f.proyecto_id', '=', 'p.proyecto_id')
            ->join('proyecto_articulos as pa', 'f.articulo_id', '=', 'pa.id')
            ->select('f.id as falla_id', 'f.materiales', 'f.costo_hh', 'p.nombre as proyecto_nombre', 'pa.nombre as articulo_nombre', 'f.fecha')
            ->whereNotNull('f.materiales')
            ->where('f.materiales', '!=', '[]')
            ->orderBy('f.id', 'desc')
            ->get();

        $fallasPendientes = $fallas->map(function ($falla) {
            $materiales = json_decode($falla->materiales, true);
            if (!is_array($materiales)) return null;

            $pendientes = array_filter($materiales, function ($mat) {
                return !isset($mat['costo']) || $mat['costo'] === '' || (float)$mat['costo'] == 0;
            });

            if (empty($pendientes)) return null;

            $falla->materiales_pendientes = array_values($pendientes); // re-index
            return $falla;
        })->filter(); // remove nulls

        return view('ERP.costosFallas', ['fallas' => $fallasPendientes]);
    }

    public function guardarCostosFalla(Request $request)
    {
        $request->validate([
            'falla_id' => 'required|exists:fallas,id',
            'materiales' => 'required|array',
            'materiales.*.material' => 'required|string',
            'materiales.*.costo' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $falla = DB::table('fallas')->where('id', $request->falla_id)->first();
            if (!$falla) {
                return response()->json(['success' => false, 'message' => 'Falla no encontrada'], 404);
            }

            $materialesOriginales = json_decode($falla->materiales, true) ?? [];
            $materialesNuevos = $request->materiales;
            
            // Create a map of new costs from the request
            $nuevosCostosMap = [];
            foreach ($materialesNuevos as $matNuevo) {
                $nuevosCostosMap[$matNuevo['material']] = (float)$matNuevo['costo'];
            }

            // Update original materials list with new costs
            $costoMaterialesTotal = 0;
            foreach ($materialesOriginales as &$matOriginal) {
                // If a new cost was submitted for this material, update it
                if (array_key_exists($matOriginal['material'], $nuevosCostosMap)) {
                    $matOriginal['costo'] = $nuevosCostosMap[$matOriginal['material']];
                }
                $costoMaterialesTotal += (float)($matOriginal['costo'] ?? 0);
            }

            $costoTotal = (float)$falla->costo_hh + $costoMaterialesTotal;

            DB::table('fallas')
                ->where('id', $request->falla_id)
                ->update([
                    'materiales' => json_encode($materialesOriginales),
                    'costo_materiales' => $costoMaterialesTotal,
                    'costo_total' => $costoTotal,
                ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Costos actualizados correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

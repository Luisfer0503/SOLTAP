<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendedor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage as FacadeStorage;


class ERPController extends Controller
{
    
      public function inicio()
      {
            return view('inicio');
      }

      public function altaArticulos()
      {     
             $proyectos = [
        (object)['id' => 1, 'nombre' => 'Casa Lomas - Cocina'],
        (object)['id' => 2, 'nombre' => 'Oficinas Centro - Escritorios'],
    ];
      
              $materiales = [
        (object)['id' => 1, 'nombre' => 'Madera de Pino'],
        (object)['id' => 2, 'nombre' => 'Melamina Blanca'],
        (object)['id' => 3, 'nombre' => 'Tela Gris Oxford'],
    ];
            return view('ERP.gestionArticulos', compact('proyectos', 'materiales')); // Lógica para mostrar el formulario de alta de artículos
      }

      public function gestionArticulos()
      {
    // Simulamos datos (en tu código real usarás Material::all() y Proyecto::all())
    $materiales = [
        (object)['id' => 1, 'nombre' => 'Madera de Pino'],
        (object)['id' => 2, 'nombre' => 'Melamina Blanca'],
        (object)['id' => 3, 'nombre' => 'Tela Gris Oxford'],
    ];

    $proyectos = [
        (object)['id' => 1, 'nombre' => 'Casa Lomas - Cocina'],
        (object)['id' => 2, 'nombre' => 'Oficinas Centro - Escritorios'],
    ];

    return view('ERP.gestionArticulos', compact('materiales', 'proyectos'));
      }


      public function seguimientoProyectos()
      {
    // Simulamos la fecha de hoy para el ejercicio
    $hoy = \Carbon\Carbon::now();
    
    // MOCK DATA: En tu sistema real esto viene de BD (Modelo Proyecto::with('cliente')...)
    $proyectos = collect([
        (object)[
            'id' => 101,
            'nombre' => 'Remodelación Cocina Lomas',
            'cliente' => 'Arq. Roberto Díaz',
            'estatus' => 'Cotización Pendiente', // Estado crítico
            'fecha_limite' => $hoy->copy()->addDay(), // ¡VENCE MAÑANA! (Alerta)
            'monto_estimado' => 45000,
            'progreso' => 20
        ],
        (object)[
            'id' => 102,
            'nombre' => 'Oficinas Coworking Centro',
            'cliente' => 'Tech Solutions SA',
            'estatus' => 'En Proceso',
            'fecha_limite' => $hoy->copy()->addDays(5),
            'monto_estimado' => 120000,
            'progreso' => 60
        ],
        (object)[
            'id' => 103,
            'nombre' => 'Recámara Principal',
            'cliente' => 'Ana Sofía Lopez',
            'estatus' => 'Nuevo',
            'fecha_limite' => $hoy->copy()->addDays(2),
            'monto_estimado' => 0, // Aún no se sabe
            'progreso' => 5
        ],
        (object)[
            'id' => 104,
            'nombre' => 'Lobby Hotel Grand',
            'cliente' => 'Hotelera Internacional',
            'estatus' => 'Cerrado / Ganado',
            'fecha_limite' => $hoy->copy()->subDays(10),
            'monto_estimado' => 350000,
            'progreso' => 100
        ],
    ]);

    // Lógica para detectar urgencias (Regla: 1 día antes)
    $urgentes = $proyectos->filter(function($p) use ($hoy) {
        // Si no está cerrado Y la fecha es mañana
        return $p->estatus !== 'Cerrado / Ganado' && 
               \Carbon\Carbon::parse($p->fecha_limite)->isSameDay($hoy->copy()->addDay());
    })->count();

    return view('ERP.seguimientoProyectos', compact('proyectos', 'urgentes'));
      }


      public function asignacionPrecios()
{
    // MOCK DATA: Esto vendría de tu BD. 
    // 'costo_produccion' es la suma automática de materiales (que ya tienes en otras tablas).
    $articulos = [
        (object)[
            'id' => 1,
            'nombre' => 'Escritorio Ejecutivo Lomas',
            'imagen' => 'https://via.placeholder.com/150', // Aquí iría la ruta real
            'costo_produccion' => 3500.00, // Costo real (Madera + Mano de obra)
            'precio_venta' => 5500.00,     // Precio sugerido o anterior
            'dimensiones' => '1.80 x 0.80 x 0.75'
        ],
        (object)[
            'id' => 2,
            'nombre' => 'Silla Eames Replica',
            'imagen' => null, 
            'costo_produccion' => 850.50,
            'precio_venta' => 1200.00,
            'dimensiones' => 'Estandar'
        ],
        (object)[
            'id' => 3,
            'nombre' => 'Credenza Archivero',
            'imagen' => 'https://via.placeholder.com/150',
            'costo_produccion' => 2100.00,
            'precio_venta' => 2800.00, // Margen bajo intencional para probar la vista
            'dimensiones' => '2.00 x 0.50 x 0.90'
        ],
    ];

    return view('ERP.asignacionPrecios', compact('articulos'));
}

    public function logistica($proyecto_id = 1)
{
    // MOCK DATA: Simulación de datos traídos de la BD
    $proyecto = (object)[
        'id' => 1,
        'nombre' => 'Oficinas Corporativo Santa Fe',
        'cliente' => 'Tech Solutions SA',
        'direccion' => 'Av. Santa Fe 450, Piso 12, CDMX',
        'es_planta_baja' => false, // Este dato viene de cuando se dio de alta al prospecto/proyecto
        'condiciones_acceso' => '' // Campo vacío para llenar aquí
    ];

    $articulos = [
        (object)[
            'id' => 101,
            'nombre' => 'Escritorio Gerencial L',
            'dimensiones' => '1.80 x 1.60m',
            'peso' => 45.5,
            'requiere_instalacion' => true, // JALADO DE GESTION ARTICULOS
            'requiere_emplaye' => true,     // JALADO DE GESTION ARTICULOS
            'comentarios_ventas' => 'El cliente pide cuidado especial con la cubierta de cristal.', // JALADO
            'comentarios_logistica' => '' // NUEVO CAMPO
        ],
        (object)[
            'id' => 102,
            'nombre' => 'Silla Operativa Malla',
            'dimensiones' => 'Estándar',
            'peso' => 12.0,
            'requiere_instalacion' => false,
            'requiere_emplaye' => false,
            'comentarios_ventas' => '',
            'comentarios_logistica' => ''
        ]
    ];

    return view('ERP.logistica', compact('proyecto', 'articulos'));
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
              'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
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

          DB::table('articulos')->insert($data);

          return redirect()->back()->with('mensaje', 'Artículo guardado correctamente');
      }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendedor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage as FacadeStorage;


class CRMController extends Controller
{
    
      public function inicio()
      {
            return view('inicio');
      }

      public function altaVendedores()
      {
            $consulta = DB::select("SELECT vendedor_id FROM vendedores ORDER BY vendedor_id DESC LIMIT 1");
            $cuantos = count($consulta);
            $sigue = ($cuantos == 0) ? 1 : $consulta[0]->vendedor_id + 1;
            
       // Obtener empresas de la base de datos
            $empresas = DB::select("SELECT empresa_id, nombre FROM empresas ORDER BY nombre ASC");
        // Obtener lista de vendedores para mostrar en la vista
        $vendedores = DB::select("SELECT * FROM vendedores ORDER BY vendedor_id DESC");

        return view('CRM.altaVendedores')
                ->with('sigue', $sigue)
                ->with('empresas', $empresas)
                ->with('vendedores', $vendedores);
      }

      public function guardarVendedor(Request $request)
       {
            $request->validate([
                  'nombre' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'apellido_paterno' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'apellido_materno' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'correo' => 'required|email|unique:vendedores,correo',
                  'telefono' => 'required|numeric',
                  'empresa' => 'required|exists:empresas,empresa_id',
                  'foto' => 'required|image|mimes:jpeg,png,jpg|max:3072'
            ]);

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('fotos', $filename, 'public');

            $vendedor = new Vendedor();
            $vendedor->nombre = $request->nombre;
            $vendedor->apellido_paterno = $request->apellido_paterno;
            $vendedor->apellido_materno = $request->apellido_materno;
            $vendedor->correo = $request->correo;
            $vendedor->telefono = $request->telefono;
            $vendedor->empresa_id = $request->empresa;
            $vendedor->foto = $path;
            $vendedor->save();

             return redirect()->route('altaVendedores')->with('mensaje', 'Vendedor guardado correctamente');
      }

        public function editarVendedor($id)
        {
            $vendedor = DB::selectOne("SELECT * FROM vendedores WHERE vendedor_id = ?", [$id]);
            if (!$vendedor) return redirect()->route('altaVendedores')->with('mensaje', 'Vendedor no encontrado');

            $empresas = DB::select("SELECT empresa_id, nombre FROM empresas ORDER BY nombre ASC");
            return view('CRM.editarVendedores', compact('vendedor','empresas'));
        }

        public function actualizarVendedor(Request $request, $id)
        {
            $request->validate([
                'nombre' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\\s]+$/',
                'apellido_paterno' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\\s]+$/',
                'apellido_materno' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\\s]+$/',
                'correo' => ['required','email', Rule::unique('vendedores','correo')->ignore($id, 'vendedor_id')],
                'telefono' => 'required|numeric',
                'empresa' => 'required|exists:empresas,empresa_id',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:6000'
            ]);

            $vendedor = DB::table('vendedores')->where('vendedor_id', $id)->first();

            if (!$vendedor) {
                return redirect()->route('altaVendedores')->with('error', 'Vendedor no encontrado.');
            }

            $data = [
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'empresa_id' => $request->empresa,
            ];

            if ($request->hasFile('foto')) {
                // Borrar foto anterior si existe
                if ($vendedor->foto) {
                    Storage::disk('public')->delete($vendedor->foto);
                }

                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('fotos', $filename, 'public');
                $data['foto'] = $path;
            }

            DB::table('vendedores')->where('vendedor_id', $id)->update($data);
            return redirect()->route('altaVendedores')->with('mensaje', 'Vendedor actualizado correctamente');
        }

        public function eliminarVendedor($id)
        {
            $vendedor = DB::table('vendedores')->where('vendedor_id', $id)->first();

            if ($vendedor) {
                // Borrar la foto del almacenamiento si existe
                if ($vendedor->foto) {
                    Storage::disk('public')->delete($vendedor->foto);
                }

                // Eliminar el registro de la base de datos
                DB::table('vendedores')->where('vendedor_id', $id)->delete();

                return redirect()->route('altaVendedores')->with('mensaje', 'Vendedor eliminado correctamente');
            }
            return redirect()->route('altaVendedores')->with('error', 'No se pudo eliminar el vendedor o no fue encontrado.');
        }

      public function altaProspectos()
      {
            $consulta = DB::select("SELECT prospecto_id FROM Prospectos ORDER BY prospecto_id DESC LIMIT 1");
            $cuantos = count($consulta);
            $sigue = ($cuantos == 0) ? 1 : $consulta[0]->prospecto_id + 1;
            
            // Obtener estados de la base de datos
            $estados = DB::select("SELECT estado_id, nombre FROM estados ORDER BY nombre ASC");
            
            // Obtener empresas de la base de datos
            $empresas = DB::select("SELECT empresa_id, nombre FROM empresas ORDER BY nombre ASC");
            
            $enfoques = DB::select("SELECT enfoque_id, nombre FROM enfoques ");
            
            $canales_distribucion= DB::select("SELECT canal_id, nombre FROM canales_distribucion ORDER BY nombre ASC");
            
            return view('CRM.altaProspectos')
                        ->with('sigue', $sigue)
                        ->with('estados', $estados)
                        ->with('empresas', $empresas)
                        ->with('enfoques', $enfoques)
                        ->with('canales', $canales_distribucion);
      }

      public function guardarProspecto(Request $request)
       {
               $request->validate([
                  'Nombre' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'ApellidoPat' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'ApellidoMat' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'Correo' => 'required|email|unique:prospectos,correo',
                  'Telefono' => 'required|numeric',
                  'IdEstado' => 'required|exists:estados,estado_id',
                  'Municipio' => 'required|string|max:100',
                  'CodigoPostal' => 'required|numeric',
                  'Calle' => 'required|string|max:255',
                  'DireccionEntrega' => 'required_if:DireccionDiferente,si|nullable|string|max:255',
                  'Maps' => 'nullable|string',
                  'Fecha' => 'required|date',
                  'Hora' => 'required',
                  'IdEmpresa' => 'required|exists:empresas,empresa_id',
                  'IdEnfoque' => 'nullable|exists:enfoques,enfoque_id',
                  'IdCanalDistribuccion' => 'nullable|exists:canales_distribucion,canal_id',
                  'NombreProyectoCompleto' => 'nullable|string|max:255',
                  'Descripcion' => 'nullable|string',
                  'TieneEnvio' => 'nullable|in:si,no',
                  'TieneIva' => 'nullable|in:si,no',
                  'IvaPorcentaje' => 'required_if:TieneIva,si|nullable|numeric|min:1|max:25',
                  'TieneDescuento' => 'nullable|in:si,no',
                  'DescuentoPorcentaje' => 'required_if:TieneDescuento,si|nullable|numeric|min:1|max:25',
            ]);

            DB::beginTransaction();
            try {
                $prospecto = new \App\Models\Prospecto();
                $prospecto->nombre = $request->Nombre;
                $prospecto->apellido_paterno = $request->ApellidoPat;
                $prospecto->apellido_materno = $request->ApellidoMat;
                $prospecto->correo = $request->Correo;
                $prospecto->telefono = $request->Telefono;
                $prospecto->estado_id = $request->IdEstado;
                $prospecto->municipio = $request->Municipio;
                $prospecto->codigo_postal = $request->CodigoPostal;
                $prospecto->calle = $request->Calle;
                // Si la dirección de entrega es diferente, guardarla; si no, usar la dirección del prospecto
                $prospecto->direccion_entrega = ($request->DireccionDiferente === 'si') ? $request->DireccionEntrega : $request->Calle . ', ' . $request->Municipio;
                $prospecto->maps = $request->Maps;
                $prospecto->fecha = $request->Fecha . ' ' . $request->Hora;
                $prospecto->empresa_id = $request->IdEmpresa;
                
                // Verificar si es Solferino Home para asignar estatus Cliente
                $nombreEmpresa = DB::table('empresas')->where('empresa_id', $request->IdEmpresa)->value('nombre');
                $esSolferino = false;
                if ($nombreEmpresa && stripos($nombreEmpresa, 'Solferino') !== false) {
                    $prospecto->estatus_id = DB::table('estatus')->where('nombre', 'Cliente')->value('estatus_id');
                    $esSolferino = true;
                } else {
                    $prospecto->estatus_id = DB::table('estatus')->where('nombre', 'Prospecto')->value('estatus_id');
                }

                $prospecto->interaccion_id = 1; // Valor fijo por defecto
                $prospecto->enfoque_id = $request->IdEnfoque;
                $prospecto->canal_id = $request->IdCanalDistribuccion;
                $prospecto->proyecto = $request->NombreProyectoCompleto;
                $prospecto->descripcion = $request->Descripcion;

                $prospecto->tiene_envio = ($request->TieneEnvio === 'si') ? 1 : 0;
                $prospecto->tiene_iva = ($request->TieneIva === 'si') ? 1 : 0;
                $prospecto->iva = ($request->TieneIva === 'si') ? $request->IvaPorcentaje : 0;
                $prospecto->tiene_descuento = ($request->TieneDescuento === 'si') ? 1 : 0;
                $prospecto->descuento = ($request->TieneDescuento === 'si') ? $request->DescuentoPorcentaje : 0;

                $prospecto->save();

                // Si es Solferino, guardar también en la tabla Clientes
                $clienteId = null;
                if ($esSolferino) {
                    $clienteId = DB::table('Clientes')->insertGetId([
                        'prospecto_id' => $prospecto->prospecto_id
                    ]);
                }

                // Crear proyecto
                $proyectoData = [
                    'prospecto_id' => $prospecto->prospecto_id,
                    'nombre' => $prospecto->proyecto,
                    'estatus' => null
                ];

                if ($clienteId) {
                    $proyectoData['cliente_id'] = $clienteId;
                }

                $proyectoId = DB::table('Proyectos')->insertGetId($proyectoData);

                // Guardar detalles del proyecto
                $detallesData = [
                    'detalles_id' => $proyectoId,
                    'prospecto_id' => $prospecto->prospecto_id,
                    'empresa_id' => $prospecto->empresa_id,
                    'maps' => $prospecto->maps,
                    'descripcion' => $prospecto->descripcion,
                    'enfoque_id' => $prospecto->enfoque_id,
                    'canal_id' => $prospecto->canal_id,
                    'direccion_entrega' => $prospecto->direccion_entrega,
                ];

                if ($clienteId) {
                    $detallesData['cliente_id'] = $clienteId;
                }

                DB::table('proyecto_detalles')->insert($detallesData);

                DB::commit();
                return redirect()->route('altaProspectos')->with('mensaje', 'Prospecto guardado correctamente');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
            }
      }

      public function reporteEstatus(Request $request)
      {
          $estatusId = $request->input('estatus_id');
          
          $query = DB::table('prospectos as p')
              ->leftJoin('Clientes as c', 'p.prospecto_id', '=', 'c.prospecto_id')
              ->leftJoin('Proyectos as pr', function($join) {
                  $join->on('p.prospecto_id', '=', 'pr.prospecto_id')
                       ->orOn('c.cliente_id', '=', 'pr.cliente_id');
              })
              ->leftJoin('estatus as e', 'p.estatus_id', '=', 'e.estatus_id')
              ->select(
                  'p.prospecto_id',
                  'pr.nombre as proyecto',
                  DB::raw("CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) as prospecto"),
                  'p.fecha as fecha_registro',
                  'p.updated_at as fecha_actualizacion',
                  'e.nombre as estatus'
              );

          if ($estatusId) {
              $query->where('p.estatus_id', $estatusId);
          }
          
          $resultados = $query->orderBy('p.fecha', 'desc')->get();
          $todosEstatus = DB::table('estatus')->orderBy('nombre')->get();

          return view('CRM.reporteEstatus', compact('resultados', 'todosEstatus', 'estatusId'));
      }

      public function cambiarEstatusVentaNoConcluida($id)
      {
          try {
              $estatus = DB::table('estatus')->where('nombre', 'Venta no concluida')->first();
              
              if ($estatus) {
                  DB::table('prospectos')
                      ->where('prospecto_id', $id)
                      ->update([
                          'estatus_id' => $estatus->estatus_id,
                          'updated_at' => now()
                      ]);
                  return redirect()->back()->with('mensaje', 'Estatus actualizado a Venta No Concluida');
              }
              return redirect()->back()->with('error', 'Estatus no encontrado');
          } catch (\Exception $e) {
              return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
          }
      }

    public function asignacionVendedor(Request $request)
     {
        // Obtener todos los prospectos (sin filtrar por estatus) para facilitar búsqueda
        $prospectos = DB::select("SELECT p.prospecto_id AS id, p.nombre, p.apellido_paterno, p.apellido_materno, CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) AS nombre_completo, p.telefono, p.correo, COALESCE(es.nombre,'') AS estatus, CONCAT(COALESCE(p.calle,''), ', ', COALESCE(p.municipio,''), ', ', COALESCE(e.nombre,'')) AS direccion, COALESCE(em.nombre,'') AS empresa, (SELECT c.cliente_id FROM clientes c WHERE c.prospecto_id = p.prospecto_id LIMIT 1) as cliente_id, COALESCE((SELECT pr.nombre FROM Proyectos pr WHERE pr.prospecto_id = p.prospecto_id ORDER BY pr.proyecto_id DESC LIMIT 1),'') AS proyecto FROM prospectos p LEFT JOIN estados e ON p.estado_id = e.estado_id LEFT JOIN estatus es ON p.estatus_id = es.estatus_id LEFT JOIN empresas em ON p.empresa_id = em.empresa_id ORDER BY p.prospecto_id DESC");
        
        // Obtener vendedores desde la base de datos
        $vendedores = DB::select("SELECT vendedor_id, CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) AS nombre FROM vendedores ORDER BY nombre ASC");
      
        $tiempo_disenno=DB::select("SELECT tiempo_id, nombre FROM tiempo_disenno");
        
        return view('CRM.asignacionVendedor')
                ->with('prospectos', $prospectos)
                ->with('vendedores', $vendedores)
                ->with('tiempo_disenno', $tiempo_disenno);
      }

      public function listarProspectos()
      {
            try {
                // Retornar lista de prospectos en JSON para el modal
                $prospectos = DB::select("SELECT p.prospecto_id AS id, p.nombre, p.apellido_paterno, p.apellido_materno, CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) AS nombre_completo, p.telefono, p.correo, COALESCE(es.nombre,'') AS estatus, COALESCE(em.nombre,'') AS empresa FROM prospectos p LEFT JOIN estatus es ON p.estatus_id = es.estatus_id LEFT JOIN empresas em ON p.empresa_id = em.empresa_id ORDER BY p.prospecto_id DESC");
                \Log::info('Prospectos cargados:', ['count' => count($prospectos)]);
                return response()->json($prospectos);
            } catch (\Exception $e) {
                \Log::error('Error en listarProspectos:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
      }

      public function listarClientes()
      {
            try {
                // Retornar lista de clientes en JSON (incluye datos del prospecto y empresa para generar nomenclatura)
                $clientes = DB::select("SELECT c.cliente_id AS id, c.prospecto_id, p.nombre, p.apellido_paterno, p.apellido_materno, p.empresa_id, COALESCE(em.nombre,'') AS empresa_nombre, CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) AS nombre_completo FROM Clientes c LEFT JOIN prospectos p ON c.prospecto_id = p.prospecto_id LEFT JOIN empresas em ON p.empresa_id = em.empresa_id ORDER BY c.cliente_id DESC");
                return response()->json($clientes);
            } catch (\Exception $e) {
                \Log::error('Error en listarClientes:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
      }

    public function clientes()
    {
        // Página de gestión de clientes - listado y posibilidad de agregar proyectos
        $clientes = DB::select("SELECT c.cliente_id AS id, c.prospecto_id, p.nombre, p.apellido_paterno, p.apellido_materno, p.empresa_id, p.maps, p.enfoque_id, p.canal_id, p.calle, p.municipio, p.estado_id, COALESCE(em.nombre,'') AS empresa_nombre, CONCAT(COALESCE(p.nombre,''), ' ', COALESCE(p.apellido_paterno,''), ' ', COALESCE(p.apellido_materno,'')) AS nombre_completo FROM Clientes c LEFT JOIN prospectos p ON c.prospecto_id = p.prospecto_id LEFT JOIN empresas em ON p.empresa_id = em.empresa_id ORDER BY c.cliente_id DESC");
        $empresas = DB::select("SELECT empresa_id, nombre FROM empresas ORDER BY nombre ASC");
        $estados = DB::select("SELECT estado_id, nombre FROM estados ORDER BY nombre ASC");
        $enfoques = DB::select("SELECT enfoque_id, nombre FROM enfoques ORDER BY nombre ASC");
        $canales = DB::select("SELECT canal_id, nombre FROM canales_distribucion ORDER BY nombre ASC");
        return view('CRM.clientes')
            ->with('clientes', $clientes)
            ->with('empresas', $empresas)
            ->with('estados', $estados)
            ->with('enfoques', $enfoques)
            ->with('canales', $canales);
    }

      public function guardarProyecto(Request $request)
      {
            $request->validate([
                'cliente_id' => 'required|exists:Clientes,cliente_id',
                'nombre_proyecto' => 'required|string|max:255',
                'cambiar_direccion' => 'nullable|in:si,no',
                'direccion_entrega' => 'nullable|string|max:255',
                'empresa_id' => 'nullable|exists:empresas,empresa_id',
                'maps' => 'nullable|string',
                'descripcion' => 'nullable|string',
                'enfoque_id' => 'nullable|exists:enfoques,enfoque_id',
                'canal_id' => 'nullable|exists:canales_distribucion,canal_id',
            ]);

            try {
                return DB::transaction(function () use ($request) {
                    $clienteId = $request->input('cliente_id');

                    // Insertar proyecto
                    $proyectoData = [
                        'cliente_id' => $clienteId,
                        'nombre' => $request->input('nombre_proyecto'),
                        'estatus' => null
                    ];
                    $proyectoId = DB::table('Proyectos')->insertGetId($proyectoData);

                    // Guardar detalles del proyecto en la tabla proyecto_detalles
                    $detallesData = [
                        'detalles_id' => $proyectoId,
                        'cliente_id' => $clienteId,
                        'empresa_id' => $request->input('empresa_id'),
                        'maps' => $request->input('maps'),
                        'descripcion' => $request->input('descripcion'),
                        'enfoque_id' => $request->input('enfoque_id'),
                        'canal_id' => $request->input('canal_id'),
                        'direccion_entrega' => ($request->input('cambiar_direccion') === 'si') ? $request->input('direccion_entrega') : null,
                    ];

                    DB::table('proyecto_detalles')->insert($detallesData);

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'mensaje' => 'Proyecto creado correctamente',
                            'cliente_id' => $clienteId
                        ], 200);
                    }
                    return redirect()->back()->with('mensaje', 'Proyecto creado correctamente');
                });
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Error al crear proyecto: ' . $e->getMessage());
            }
      }

      public function editarProspecto($id)
      {
            $prospecto = DB::selectOne("SELECT * FROM prospectos WHERE prospecto_id = ?", [$id]);
            if (!$prospecto) return redirect()->route('altaProspectos')->with('error', 'Prospecto no encontrado');

            $estados = DB::select("SELECT estado_id, nombre FROM estados ORDER BY nombre ASC");
            $empresas = DB::select("SELECT empresa_id, nombre FROM empresas ORDER BY nombre ASC");
            $estatus = DB::select("SELECT estatus_id, nombre FROM estatus ORDER BY nombre ASC");
            $enfoques = DB::select("SELECT enfoque_id, nombre FROM enfoques ORDER BY nombre ASC");
            $canales_distribucion = DB::select("SELECT canal_id, nombre FROM canales_distribucion ORDER BY nombre ASC");

            return view('CRM.editarProspecto')
                        ->with('prospecto', $prospecto)
                        ->with('estados', $estados)
                        ->with('empresas', $empresas)
                        ->with('estatus', $estatus)
                        ->with('enfoques', $enfoques)
                        ->with('canales', $canales_distribucion);
      }

      public function actualizarProspecto(Request $request, $id)
      {
            $request->validate([
                  'Nombre' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'ApellidoPat' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'ApellidoMat' => 'required|string|max:100|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
                  'Correo' => ['required','email', Rule::unique('prospectos','correo')->ignore($id, 'prospecto_id')],
                  'Telefono' => 'required|numeric',
                  'IdEstado' => 'required|exists:estados,estado_id',
                  'Municipio' => 'required|string|max:100',
                  'CodigoPostal' => 'required|numeric',
                  'Calle' => 'required|string|max:255',
                  'DireccionEntrega' => 'required_if:DireccionDiferente,si|nullable|string|max:255',
                  'Maps' => 'nullable|string',
                  'IdEmpresa' => 'required|exists:empresas,empresa_id',
                  'IdEnfoque' => 'nullable|exists:enfoques,enfoque_id',
                  'IdCanalDistribuccion' => 'nullable|exists:canales_distribucion,canal_id',
                  'NombreProyectoCompleto' => 'nullable|string|max:255',
                  'Descripcion' => 'nullable|string',
                  'TieneEnvio' => 'nullable|in:si,no',
                  'TieneIva' => 'nullable|in:si,no',
                  'IvaPorcentaje' => 'required_if:TieneIva,si|nullable|numeric|min:1|max:25',
                  'TieneDescuento' => 'nullable|in:si,no',
                  'DescuentoPorcentaje' => 'required_if:TieneDescuento,si|nullable|numeric|min:1|max:25',
            ]);

            $data = [
                'nombre' => $request->Nombre,
                'apellido_paterno' => $request->ApellidoPat,
                'apellido_materno' => $request->ApellidoMat,
                'correo' => $request->Correo,
                'telefono' => $request->Telefono,
                'estado_id' => $request->IdEstado,
                'municipio' => $request->Municipio,
                'codigo_postal' => $request->CodigoPostal,
                'calle' => $request->Calle,
                'direccion_entrega' => ($request->DireccionDiferente === 'si') ? $request->DireccionEntrega : $request->Calle . ', ' . $request->Municipio,
                'maps' => $request->Maps,
                'empresa_id' => $request->IdEmpresa,
                'enfoque_id' => $request->IdEnfoque,
                'canal_id' => $request->IdCanalDistribuccion,
                'proyecto' => $request->NombreProyectoCompleto,
                'descripcion' => $request->Descripcion,
                'tiene_envio' => ($request->TieneEnvio === 'si') ? 1 : 0,
                'tiene_iva' => ($request->TieneIva === 'si') ? 1 : 0,
                'iva' => ($request->TieneIva === 'si') ? $request->IvaPorcentaje : 0,
                'tiene_descuento' => ($request->TieneDescuento === 'si') ? 1 : 0,
                'descuento' => ($request->TieneDescuento === 'si') ? $request->DescuentoPorcentaje : 0,
            ];

            DB::table('prospectos')->where('prospecto_id', $id)->update($data);
            return redirect()->route('altaProspectos')->with('mensaje', 'Prospecto actualizado correctamente');
      }

      public function eliminarProspecto($id)
      {
            $deleted = DB::delete("DELETE FROM prospectos WHERE prospecto_id = ?", [$id]);
            if ($deleted) {
                return redirect()->route('altaProspectos')->with('mensaje', 'Prospecto eliminado correctamente');
            }
            return redirect()->route('altaProspectos')->with('error', 'No se pudo eliminar el prospecto');
      }

      public function guardarAsignacion(Request $request)
       {
            $request->validate([
                'IdProspecto' => 'required|exists:prospectos,prospecto_id',
                'IdVendedor' => 'required|exists:vendedores,vendedor_id',
                'tiempo_id' => 'required|exists:tiempo_disenno,tiempo_id',
                'proyecto_id' => 'required|exists:Proyectos,proyecto_id',
            ]);

            try {
                DB::table('proyecto_detalles')
                    ->where('detalles_id', $request->proyecto_id)
                    ->update([
                        'vendedor_id' => $request->IdVendedor,
                        'tiempo_id' => $request->tiempo_id
                    ]);

                return redirect()->route('asignacionVendedor')->with('mensaje', 'Vendedor asignado correctamente');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al asignar vendedor: ' . $e->getMessage());
            }
        }

      public function listarAsignaciones()
      {
            try {
                $asignaciones = DB::select("
                    SELECT 
                        COALESCE(p.prospecto_id, c.prospecto_id) AS cliente_id,
                        pr.proyecto_id,
                        CONCAT(COALESCE(p.nombre, p2.nombre, ''), ' ', COALESCE(p.apellido_paterno, p2.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, p2.apellido_materno, '')) AS prospecto,
                        CONCAT(COALESCE(v.nombre,''), ' ', COALESCE(v.apellido_paterno,''), ' ', COALESCE(v.apellido_materno,'')) AS vendedor,
                        v.vendedor_id,
                        COALESCE(pr.nombre, 'Sin Proyecto') as proyecto,
                        COALESCE(t.nombre, 'N/A') as tiempo
                    FROM Proyectos pr
                    LEFT JOIN prospectos p ON pr.prospecto_id = p.prospecto_id
                    LEFT JOIN Clientes c ON pr.cliente_id = c.cliente_id
                    LEFT JOIN prospectos p2 ON c.prospecto_id = p2.prospecto_id
                    JOIN proyecto_detalles pd ON pr.proyecto_id = pd.detalles_id
                    LEFT JOIN vendedores v ON pd.vendedor_id = v.vendedor_id
                    LEFT JOIN tiempo_disenno t ON pd.tiempo_id = t.tiempo_id
                    WHERE pd.vendedor_id IS NOT NULL
                    ORDER BY pr.proyecto_id DESC
                ");
                return response()->json($asignaciones);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
      }

      public function obtenerProyectosProspecto($id)
      {
          try {
              // Buscar si este prospecto tiene un cliente_id asociado
              $cliente = DB::table('Clientes')->where('prospecto_id', $id)->first();
              $clienteId = $cliente ? $cliente->cliente_id : null;

              $query = DB::table('Proyectos')
                  ->leftJoin('proyecto_detalles', 'Proyectos.proyecto_id', '=', 'proyecto_detalles.detalles_id')
                  ->leftJoin('empresas', 'proyecto_detalles.empresa_id', '=', 'empresas.empresa_id')
                  ->select(
                      'Proyectos.proyecto_id',
                      'Proyectos.nombre',
                      'Proyectos.estatus',
                      'proyecto_detalles.descripcion',
                      'proyecto_detalles.maps',
                      'empresas.nombre as empresa'
                  );

              if ($clienteId) {
                  $query->where(function($q) use ($id, $clienteId) {
                      $q->where('Proyectos.prospecto_id', $id)
                        ->orWhere('Proyectos.cliente_id', $clienteId);
                  });
              } else {
                  $query->where('Proyectos.prospecto_id', $id);
              }

              $proyectos = $query->orderBy('Proyectos.proyecto_id', 'desc')->get();

              return response()->json($proyectos);
          } catch (\Exception $e) {
              return response()->json(['error' => $e->getMessage()], 500);
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
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductoCasa;
use App\Models\CategoriasGuardar;
use App\Models\FacturaCasa;
use App\Models\MesasCasa;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
class HeladeriaCasa extends Controller
{
    /**
     * Mostrar la vista de productos.
     */
    public function IndexProductos()
    {
        return view('Administrador/ProductosCasa');
    }

    /**
     * Registrar un nuevo producto.
     */
  public function RegistrarProductos(Request $request)
    {
        $producto = new ProductoCasa();
        $producto->Nombre = $request->Nombre;
        $producto->Precio = $request->precio; // ojo: mantenlo consistente (Precio con P mayúscula)
        $producto->Categoria = $request->Categoria;
        $producto->save();

        return response()->json(['mensaje' => 'Producto registrado correctamente']);
    }

    public function ListarProductos(Request $request)
    {
        $buscar = $request->query('buscar');

        // Inicializar el query
        $query = ProductoCasa::query();

        if ($buscar && trim($buscar) !== '') {
            $query->where('Nombre', 'LIKE', '%' . $buscar . '%');
        }

        $resultados = $query->orderBy('id', 'desc')->paginate(8);

        return response()->json([
            'pagination' => [
                'total'        => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page'     => $resultados->perPage(),
                'last_page'    => $resultados->lastPage(),
                'from'         => $resultados->firstItem(),
                'to'           => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items()
        ]);
    }

    public function ActualizarProducto(Request $request)
    {
        $producto = ProductoCasa::where('id', $request->id)->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado o no autorizado'], 403);
        }

        $producto->Nombre = $request->Nombre;
        $producto->Precio = $request->Precio;
        $producto->Categoria = $request->Categoria;
        $producto->save();

        return response()->json(['mensaje' => 'Producto actualizado correctamente']);
    }

    public function EliminarProducto($id)
    {
        $producto = ProductoCasa::where('id', $id)->first();

        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado o no autorizado'], 403);
        }

        $producto->delete();

        return response()->json(['mensaje' => 'Producto eliminado correctamente']);
    }

    public function GuardarCategoria(Request $request){
     $Nombre = $request->NombreCategoria;
     $categoria = new CategoriasGuardar();
     $categoria->Nombre = $Nombre; 
     $categoria->save();
    }
    
    public function ListarCategorias(){

     $ListarC = CategoriasGuardar::all();
     return $ListarC;
     
    }
    public function IndexPerfilCasa()
    {
        return view('Administrador/PerfilCasa');
    }
    public function NavCasa()
    {
        $ahora = Carbon::now('America/Bogota');

        // Si la hora actual es antes de las 5:00 a. m., usamos el día anterior
        if ($ahora->hour < 5) {
            $inicioDia = Carbon::yesterday('America/Bogota')->setTime(5, 0, 0);
        } else {
            $inicioDia = Carbon::today('America/Bogota')->setTime(5, 0, 0);
        }

        $finDia = $inicioDia->copy()->addDay()->subSecond(); // Hasta las 4:59:59 del día siguiente

        // Ganancias de domicilio
        $GananciasDomiDiarias = FacturaCasa::whereBetween('fecha', [$inicioDia, $finDia])
            ->where('estado', 0)
            ->sum('ValorDomi');

        // Ganancias totales
        $gananciasDiarias = FacturaCasa::whereBetween('fecha', [$inicioDia, $finDia])
            ->where('estado', 0)
            ->sum('total');

        // Facturas del día
        $facturasDeHoy = FacturaCasa::whereBetween('fecha', [$inicioDia, $finDia])
            ->where('estado', 0)
            ->get();

        $detallesAgrupados = [];

        foreach ($facturasDeHoy as $factura) {
            $descripcion = json_decode($factura->Descripcion, true);

            if (is_array($descripcion)) {
                foreach ($descripcion as $item) {
                    if (isset($item['id']) && isset($item['cantidad'])) {
                        $producto = ProductoCasa::find($item['id']);

                        if ($producto) {
                            $id = $producto->id;
                            $nombre = $producto->Nombre;
                            $precioCompra = $producto->Precio;
                            $cantidad = $item['cantidad'];

                            if (isset($detallesAgrupados[$id])) {
                                $detallesAgrupados[$id]['cantidad'] += $cantidad;
                                $detallesAgrupados[$id]['total_compra'] += $cantidad * $precioCompra;
                            } else {
                                $detallesAgrupados[$id] = [
                                    'id' => $id,
                                    'nombre' => $nombre,
                                    'cantidad' => $cantidad,
                                    'precio_compra' => $precioCompra,
                                    'total_compra' => $cantidad * $precioCompra,
                                ];
                            }
                        }
                    }
                }
            }
        }

        $detallesProductos = array_values($detallesAgrupados);

        usort($detallesProductos, function ($a, $b) {
            return $b['cantidad'] <=> $a['cantidad'];
        });

        $ProductosVendidosHoy = array_sum(array_column($detallesProductos, 'cantidad'));

        return response()->json([
            'GananciasDomiDiarias' => $GananciasDomiDiarias,
            'ganancias_diarias' => $gananciasDiarias,
            'ProductosVendidosHoy' => $ProductosVendidosHoy,
            'detallesProductos' => $detallesProductos,
        ]);
    }

    public function getMonthlyDataCasa()
    {
        try {
            $mesActual = date('n'); // Mes actual (1-12)
            $anioActual = date('Y'); // Año actual

            $results = DB::table('facturacasa')
                ->select(
                    DB::raw('MONTH(fecha) as month'),
                    DB::raw('SUM(CAST(total AS DECIMAL(10,2))) AS total_mensual')
                )
                ->whereYear('fecha', $anioActual)
                ->where('estado', 0)
                ->groupBy(DB::raw('MONTH(fecha)'))
                ->orderBy(DB::raw('MONTH(fecha)'))
                ->get();

            $data = [
                'total_mensual' => array_fill(0, 12, 0),
            ];

            foreach ($results as $row) {
                $monthIndex = $row->month - 1;
                $data['total_mensual'][$monthIndex] = $row->total_mensual;
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar la solicitud.'], 500);
        }
    }

    public function SacarResumenCasa()
    {
        // Filtra solo las facturas con estado = 0
        $Datos = FacturaCasa::where('estado', 0)->get();

        $resumen = [];

        foreach ($Datos as $factura) {
            $descripcion = json_decode($factura->Descripcion, true); // Convertir JSON a array

            if (is_array($descripcion)) {
                foreach ($descripcion as $producto) {
                    $id = $producto['id'] ?? null;
                    $nombre = $producto['Nombre'] ?? null;
                    $cantidad = $producto['cantidad'] ?? 0;
                    $categoria = strtolower($producto['Categoria'] ?? '');

                    // Saltar si no tiene ID o si es una bebida o adicional
                    if ($id === null || $categoria === 'bebidas' || $categoria === 'adicionales') {
                        continue;
                    }

                    if (!isset($resumen[$id])) {
                        $resumen[$id] = [
                            'nombre' => $nombre,
                            'cantidad' => 0
                        ];
                    }

                    $resumen[$id]['cantidad'] += $cantidad;
                }
            }
        }

        // Ordenar de mayor a menor por cantidad
        uasort($resumen, function ($a, $b) {
            return $b['cantidad'] <=> $a['cantidad'];
        });

        return $resumen;
    }


    //// fucniones de la factura DE HELADOS
     public function IndexFacturaH()
    {
        return view('Administrador/FacturaHelados');
    }
       public function ListarP(Request $request)
        {
            $busqueda = $request->Busqueda;

            if ($busqueda) {
                $productos = ProductoCasa::where('Categoria', 'LIKE', '%' . $busqueda . '%')
                    ->get();
            } else {
                $productos = ProductoCasa::all();
            }

            return response()->json($productos);
        }
        public function Facturas(Request $request)
        {
          

            $numeroMesa = $request->input('AgregarMesa');
            $productosNuevos = $request->input('productos');
            $observacion  = $request->input('observacion') ?: 0; 

          

            // Buscar la factura solo por mesa
            $mesa = FacturaCasa::where('Mesa', $numeroMesa)->first();

         

            if ($mesa) {
                // ✅ Actualizar factura existente
                $descripcionActual = json_decode($mesa->Descripcion, true) ?? [];

               

                $descripcionActual = array_merge($descripcionActual, $productosNuevos);


                $mesa->Descripcion = json_encode($descripcionActual);

                $totalNuevo = 0;
                foreach ($productosNuevos as $producto) {
                    $totalNuevo += $producto['Precio'] * $producto['cantidad'];
                }

               

                $mesa->total += $totalNuevo;

                $observacionExistente = $mesa->observacion ?? '';
                $mesa->observacion = trim($observacionExistente . ', ' . $observacion);

                $mesa->save();

               
            } else {
                // ✅ Crear nueva factura si no existe
                $productos    = $request->input('productos');
                $nombre       = $request->input('nombre')     ?: 0;
                $direccion    = $request->input('direccion')  ?: 0;
                $telefono     = $request->input('telefono')   ?: 0;
                $observacion  = $request->input('observacion') ?: 0;
                $total        = $request->input('total')      ?: 0;
                $numeroMesa   = $request->input('numeroMesa') ?: 0;
                $valorDomicilio   = $request->input('valorDomicilio') ?: 0;

              

                $Guardar = new FacturaCasa();
                $Guardar->Nombre      = $nombre;
                $Guardar->DIrrecion   = $direccion;
                $Guardar->Telefono    = $telefono;
                $Guardar->observacion = $observacion;
                $Guardar->Mesa        = $numeroMesa;
                $Guardar->Descripcion = json_encode($productos);
                $Guardar->total       = $total;
                $Guardar->estado      = 0; // ✅ Factura abierta
                $Guardar->ValorDomi   = $valorDomicilio;
                
                $Guardar->save();

          

                // Cambiar el estado de la mesa si existe
                $mesa = MesasCasa::where('Nombre', $numeroMesa)->first();
                if ($mesa) {
                    $mesa->estado = 1;
                    $mesa->save();
                }

                
            }
        }
        public function MesasDisponibles()
        {
            $MesasActivas = MesasCasa::where('estado', 0)->get();
            $MesasAgregar = MesasCasa::where('estado', 1)->get();

            return [
                'mesas_activas' => $MesasActivas,
                'mesas_agregar' => $MesasAgregar,
            ];
        }


        ///7 fucniones de pedidos
    public function IndexPedidos()
    {
        return view('Administrador/PedidosCasa');
    }
    public function GuardarMesa(Request $request)
    {
        $exists = MesasCasa::where('nombre', $request->nombre)->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Ya existe una mesa con ese número'
            ], 422);
        }

        $mesa = new MesasCasa();
        $mesa->nombre = $request->nombre;
        $mesa->estado = 0;
        $mesa->save();

        return response()->json([
            'message' => 'Mesa guardada correctamente',
            'mesa' => $mesa
        ], 201);
    }

    public function ListarMesas()
        {
            $datos = MesasCasa::all();
            return $datos;
    }

    public function Datos(Request $request)
        {    
            
            $mesa = MesasCasa::where('id', $request->id)->first();

            if (!$mesa) {
                return response()->json(['error' => 'Mesa no encontrada'], 404);
            }

            $Nombre = $mesa->Nombre;

            $factura = FacturaCasa::where('Mesa', $Nombre)->first();

            if (!$factura) {
                return response()->json(['error' => 'Factura no encontrada'], 404);
            }

            $descripcionStr = $factura->Descripcion;

            $descripcionArray = json_decode($descripcionStr, true);
            if (is_string($descripcionArray)) {
                $descripcionArray = json_decode($descripcionArray, true);
            }

            $factura->descripcion = $descripcionArray;

            return response()->json($factura);
    }

    public function liberarMesa(Request $request)
    {
        $nombreMesa = $request->Nombre;
        $tipoPago = $request->tipoPago;

        $mesa = MesasCasa::where('Nombre', $nombreMesa)->first();

        if (!$mesa) {
            return response()->json(['error' => 'Mesa no encontrada'], 404);
        }

        $mesa->estado = 0;
        $mesa->save();

        $factura = FacturaCasa::where('Mesa', $nombreMesa)->first();

        if ($factura) {
            $factura->Mesa = 0;
            $factura->Mp = $tipoPago;
            $factura->save();
        }

        return response()->json(['message' => 'Mesa liberada correctamente']);
    }

    public function Domis()
    {
        $facturas = FacturaCasa::where('Nombre', '!=', '0')
                            ->orderBy('Domi', 'asc')
                            ->orderBy('id', 'desc')
                            ->paginate(10);

        return response()->json([
            'pagination' => [
                'total'        => $facturas->total(),
                'current_page' => $facturas->currentPage(),
                'per_page'     => $facturas->perPage(),
                'last_page'    => $facturas->lastPage(),
                'from'         => $facturas->firstItem(),
                'to'           => $facturas->lastItem(),
            ],
            'resultados' => $facturas->items(),
        ]);
    }

  public function Entrega(Request $request)
    {
        $factura = FacturaCasa::where('id', $request->id)->first();

        if (!$factura) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }

        // Validar si ya está entregada
        if ($factura->Domi == 1) {
            return response()->json([
                'mensaje' => 'Este pedido ya fue entregado anteriormente',
                'factura' => $factura
            ], 200);
        }

        // Si no, marcar como entregada
        $factura->Mp = $request->metodo_pago;
        $factura->Domi = 1;
        $factura->save();

        return response()->json([
            'mensaje' => 'Factura marcada como entregada correctamente',
            'factura' => $factura
        ]);
    }


    public function SacarDescripcion(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $factura = FacturaCasa::where('id', $request->id)->first();

        if (!$factura) {
            return response()->json([
                'error' => 'Factura no encontrada'
            ], 404);
        }

        $descripcion = json_decode($factura->Descripcion, true);

    return response()->json([
            'descripcion' => $descripcion
        ]);

    }
    /// domiclio 
     public function IndexDomi()
    {
        return view('Administrador/DomiCasa');
    }
        public function IndexControlFacturas()
    {
        return view('Administrador/ControlFacturas');
    }
    public function FacturasControl(Request $request)
    {
        $Busqueda = $request->BUsqueda;

        // Base: solo facturas con estado 0
        $query = FacturaCasa::where('estado', 0);
    if ($Busqueda === 'domicilio') {
        $query->where('Mesa', 0)
            ->where('Nombre', '!=', '0');
    } elseif ($Busqueda === 'mesa') {
        $query->where('Nombre', '0')
            ->where('DIrrecion', '0');
}


        $resultados = $query->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'pagination' => [
                'total'        => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page'     => $resultados->perPage(),
                'last_page'    => $resultados->lastPage(),
                'from'         => $resultados->firstItem(),
                'to'           => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items()
        ]);
    }

        public function eliminar(Request $request)
{
    $id = $request->id;

    // Obtener el número de mesa de la factura
    $Idmesa = FacturaCasa::where('id', $id)->value('Mesa');

    // Buscar la mesa
    $mesa = MesasCasa::where('Nombre', $Idmesa)->first();
    if (!$mesa) {
        return response()->json(['mensaje' => 'Mesa no encontrada'], 404);
    }

    // Cambiar el estado de la mesa a 0
    $mesa->estado = 0;
    $mesa->save();

    // Buscar y eliminar la factura
    $pedido = FacturaCasa::find($id);
    if ($pedido) {
        $pedido->delete();
    }

    return response()->json(['mensaje' => 'Mesa liberada y factura eliminada correctamente']);
}



    public function Ver(Request $request)
    {
        $id = $request->id;

        $factura = FacturaCasa::find($id);

        if ($factura) {
            $datos = $factura->toArray();
            $datos['Descripcion'] = json_decode($factura->Descripcion);

            return response()->json($datos);
        } else {
            return response()->json([
                'error' => 'Factura no encontrada'
            ], 404);
        }
    }

    public function ganancias(Request $request)
    {
        $inicio = $request->input('inicio');
        $final = $request->input('final');
        $tipo = $request->input('tipo'); 

        if (!$inicio || !$final) {
            return response()->json(['error' => 'Fechas no válidas'], 400);
        }

        $query = FacturaCasa::whereDate('fecha', '>=', $inicio)
                        ->whereDate('fecha', '<=', $final)
                        ->where('estado', 0);

        if ($tipo === 'domicilio') {
            $query->where('Nombre', '!=', '0'); // domicilio
        } elseif ($tipo === "mesa") {
            $query->where('Nombre', '0'); // mesa
        }

        $totalGanancias = $query->sum('total');

        return response()->json([
            'total' => $totalGanancias
        ]);
    }


    /// rutas de ganacias 

    public function IndexGanancias(){
    return view('Administrador/Ganacias');

    }
public function DatosV()
{
    $fechaInicio = request()->get('fechaInicio');
    $fechaFinal  = request()->get('fechaFinal');

    $facturasQuery = FacturaCasa::select('id', 'fecha', 'Descripcion','Mp')
        ->orderBy('id', 'desc');
   
    if ($fechaInicio && $fechaFinal) {
        $fechaInicio = $fechaInicio . ' 00:00:00';
        $fechaFinal  = $fechaFinal . ' 23:59:59';

        $facturasQuery->whereBetween('fecha', [$fechaInicio, $fechaFinal]);
    }

    $facturas = $facturasQuery->get();

    // Agrupar productos por id y nombre
    $productosAgrupados = [];

    foreach ($facturas as $factura) {
        $detalleArray = json_decode($factura->Descripcion, true);

        foreach ($detalleArray as $producto) {
            $clave = $factura->id . '-' . $producto['Nombre'];

            if (!isset($productosAgrupados[$clave])) {
                $productosAgrupados[$clave] = [
                    'ID'             => $factura->id,
                    'Fecha'          => $factura->fecha,   // fecha tal cual viene
                    'Nombre'         => $producto['Nombre'],
                    'Cantidad'       => 0,
                    'PrecioUnitario' => $producto['Precio'],
                    'PrecioTotal'    => 0,
                    'Mp' =>           $factura->Mp
                ];
            }

            $productosAgrupados[$clave]['Cantidad'] += $producto['cantidad'];
            $productosAgrupados[$clave]['PrecioTotal'] += $producto['cantidad'] * $producto['Precio'];

            // Adicionales
            if (!empty($producto['AdicionalesDetalle'])) {
                foreach ($producto['AdicionalesDetalle'] as $adicional) {
                    $claveAdicional = $factura->id . '-' . $adicional['Nombre'];

                    if (!isset($productosAgrupados[$claveAdicional])) {
                        $productosAgrupados[$claveAdicional] = [
                            'ID'             => $factura->id,
                            'Fecha'          => $factura->fecha,
                            'Nombre'         => $adicional['Nombre'],
                            'Cantidad'       => 0,
                            'PrecioUnitario' => $adicional['Precio'],
                            'PrecioTotal'    => 0
                        ];
                    }

                    $productosAgrupados[$claveAdicional]['Cantidad'] += 1;
                    $productosAgrupados[$claveAdicional]['PrecioTotal'] += $adicional['Precio'];
                }
            }
        }
    }

    // Pasar a array normal
    $productos = array_values($productosAgrupados);
    
    // Paginación manual
    $page = request()->get('page', 1);
    $perPage = 15;
    $offset = ($page - 1) * $perPage;

    $productosPaginados = new LengthAwarePaginator(
        array_slice($productos, $offset, $perPage),
        count($productos),
        $perPage,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );
    
    return response()->json([
        'pagination' => [
            'total'        => $productosPaginados->total(),
            'current_page' => $productosPaginados->currentPage(),
            'per_page'     => $productosPaginados->perPage(),
            'last_page'    => $productosPaginados->lastPage(),
            'from'         => $productosPaginados->firstItem(),
            'to'           => $productosPaginados->lastItem(),
        ],
        'resultados' => $productosPaginados->items(),
        'resultadosSin' => $productos
    ]);
}

public function eliminarCasa(Request $request)
{
    $id = $request->id;

    // Buscar la factura por ID
    $pedido = FacturaCasa::find($id);

    if (!$pedido) {
        return response()->json([
            'mensaje' => 'Factura no encontrada',
        ], 404);
    }

    // Eliminar la factura
    $pedido->delete();

    return response()->json([
        'mensaje' => 'Mesa liberada y factura eliminada correctamente'
    ]);
}


}
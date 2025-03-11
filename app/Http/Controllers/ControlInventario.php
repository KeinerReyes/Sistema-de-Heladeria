<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\Lociones;
use App\Models\FacturasEliminadas;
use App\Models\Clientes;
use App\Models\ProductosModel;
use App\Models\GananciasDIarias;
use Carbon\Carbon;
use App\Models\vendidos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ControlInventario extends Controller
{
    public function ControlInventarioIndex(){
        return view ('Administrador/Controlinventario');
    }
    public function sacarInformacion(Request $request)
    {
        $orden_servicio = $request->input('orden_servicio');
    
        $query = Factura::query();
        
        if (!empty($orden_servicio)) {
            $query->where('orden_servicio', 'like', "%{$orden_servicio}%");
        }
    
        $resultados = $query->orderBy('orden_servicio', 'desc')->paginate(15);
    
        // Retorna los resultados con paginación
        return response()->json([
            'pagination' => [
                'total' => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page' => $resultados->perPage(),
                'last_page' => $resultados->lastPage(),
                'from' => $resultados->firstItem(),
                'to' => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items(),
        ]);
    }
    
    public function EliminarFactura(Request $request){ //// Eliminar Factura y guarda en facturas eliminadas !!!
        $orden_servicio = $request->orden_servicio; 
        $Problema = $request->Problema;

        $GuardarDatos = Factura::where('orden_servicio', $orden_servicio)->first();
        $GuardarDatosBorrados = new FacturasEliminadas();
        $GuardarDatosBorrados->orden_servicio = $GuardarDatos->orden_servicio;
        $GuardarDatosBorrados->nombre = $GuardarDatos->nombre;
        $GuardarDatosBorrados->documento = $GuardarDatos->documento;
        $GuardarDatosBorrados->gmail = $GuardarDatos->gmail;
        $GuardarDatosBorrados->telefono = $GuardarDatos->telefono;
        $GuardarDatosBorrados->descripcion = $GuardarDatos->descripcion; 
        $GuardarDatosBorrados->total = $GuardarDatos->totalF;
        $GuardarDatosBorrados->fecha_de_creacion = $GuardarDatos->fecha_de_creacion;
        $GuardarDatosBorrados->Problema =  $Problema;

        $GuardarDatosBorrados->save();
        $Eliminar = Factura::where('orden_servicio', $orden_servicio)->delete();
    }
    public function SacarInformacioC(Request $request)
{
    $documento = $request->documento;
    $orden_servicio = $request->orden_servicio;

    $cliente = Clientes::where('documento', $documento)->get();

    

    $totalF = 0;
    $descripcionArray = [];

    $factura = Factura::where('orden_servicio', $orden_servicio)->first();

    if ($factura) {
        $descripcionJson = $factura->descripcion;
        $descripcionArray = json_decode($descripcionJson, true);   

        foreach ($descripcionArray as $item) {
            if (isset($item['precio'])) {
                $totalF += $item['precio'];
            }
        }
    }

    return response()->json([
        'cliente' => $cliente,
        'descripcion' => $descripcionArray,
        'TotalF' => $totalF,
        'orden' => $orden_servicio,
    ]);
    }
    public function DevolucionProductos(Request $request)// traer los datos e la factura para realizar la devolucion
    {
        $orden_servicio = $request->orden_servicio;
        $datos = Factura::where('orden_servicio', $orden_servicio)->first();
    
        if ($datos) {
            $descripcionArray = json_decode($datos->descripcion, true);
    
            return response()->json([
                'orden_servicio' => $orden_servicio,
                'descripcion' => $descripcionArray
            ]);
        } else {
            return response()->json([
                'error' => 'No data found for the given orden_servicio'
            ], 404);
        }
    }
    public function DevolerInventario(Request $request) { /// eliminar el producto y volver a poner en el invneatrio
       
        $orden_servicio = $request->orden_servicio;
        $ids_productos = $request->input('ids_productos');
        $factura = Factura::where('orden_servicio', $orden_servicio)->first();

        if ($factura && isset($factura->descripcion)) {
            $descripcion = json_decode($factura->descripcion, true);
            if (is_array($descripcion)) {
                foreach ($descripcion as $item) {
                    if (in_array($item['id_productos'], $ids_productos) && isset($item['pr']) && $item['pr'] == 1) {
                        return response()->json([
                            'error' => 'No se puede devolver. El producto con id ' . $item['id_productos'] . ' contiene el valor prohibido "pr = 1".'
                        ], 400);
                    }
                }
            }
        }
    
            if ($factura) {
                $descripcion = json_decode($factura->descripcion, true);
                $id_productos = array_column($descripcion, 'id_productos');

                \DB::table('productos')
                ->whereIn('id_productos', $id_productos)
                ->update(['estado' => 0]); 


                if (is_array($descripcion)) {
                    $totalRestar = 0;
                    $descripcion = array_filter($descripcion, function($producto) use ($ids_productos, &$totalRestar, $factura) {
                        if (in_array($producto['id_productos'], $ids_productos)) {
                            if ($factura->totalF > 0) {
                                $totalRestar = 0;
                            }
                            return false; 
                        }
                        return true;
                    });
                    $factura->totalF -= $totalRestar;
                    if ($factura->totalF < 0) {
                        $factura->totalF = 0; 
                    } 
                    $factura->descripcion = json_encode($descripcion);
                    $factura->save();                    

                return response()->json(['message' => 'Productos eliminados correctamente.']);
            } else {
                return response()->json(['message' => 'Formato de descripción incorrecto.'], 404);
            }
        } 
    }
    /////Rutas de stock
    public function stock(){
    return view ('Administrador/stock');
    }
    public function SacarDatosSemiAgotados() /// Contar los produtos para mira cuales etsa agotados 
        {

            $productosSemiAgotados = \DB::table('productos')
            ->select('categoria', 'talla')
            ->groupBy('categoria', 'talla')
            ->get();
        
        $resultados = [];
        
        foreach ($productosSemiAgotados as $producto) {
            if ($producto->talla === 'UNICA') {
                // Si la talla es 'UNICA', contamos solo por la categoría
                $cantidad = \DB::table('productos')
                    ->where('categoria', $producto->categoria)
                    ->count();
            } else {
                // De lo contrario, contamos por categoría y talla
                $cantidad = \DB::table('productos')
                    ->where('categoria', $producto->categoria)
                    ->where('talla', $producto->talla)
                    ->count();
            }
        
            $resultados[] = [
                'categoria' => $producto->categoria,
                'talla' => $producto->talla,
                'cantidad' => $cantidad,
            ];
        }
        
        return $resultados;
        
    }
     public function SacarDatosAgotados () {
        $productosSemiAgotados = \DB::table('productos')
        ->select('categoria', 'talla')
        ->groupBy('categoria', 'talla')
        ->get();

        $resultados = [];

        foreach ($productosSemiAgotados as $producto) {
            $cantidad = \DB::table('productos')
                ->where('categoria', $producto->categoria)
                ->when($producto->talla === 'ÚNICA', function ($query) {
                    return $query->where('talla', 'ÚNICA');
                }, function ($query) use ($producto) {
                    return $query->where('talla', $producto->talla);
                })
                ->count();

            // Agregar el resultado al array como un array asociativo
            $resultados[] = [
                'categoria' => $producto->categoria,
                'talla' => $producto->talla,
                'cantidad' => $cantidad
            ];
        }

        return $resultados;
     }
     //// funciones de control de invnetario de cantidad
     public function ControlCantidad (){
        return view ('Administrador/ControlC');
    }
    public function SacarInformacioControlC(Request $request)
    {
        $categoria = $request->input('categoria');
        $talla = $request->input('talla');
    
        $perPage = 12;
    
        $query = ProductosModel::select('categoria', 'talla', DB::raw('COUNT(*) as cantidad'))
            ->where('estado', 0) 
            ->groupBy('categoria', 'talla')
            ->havingRaw('COUNT(*) > 0')
            ->orderBy('categoria', 'asc')
            ->orderBy('talla', 'asc');
    
        if ($categoria) {
            $query->where('categoria', $categoria);
        }
    
        if ($talla) {
            $query->where('talla', $talla);
        }
    
        $resultados = $query->paginate($perPage);
    
        return response()->json([
            'pagination' => [
                'total'        => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page'     => $resultados->perPage(),
                'last_page'    => $resultados->lastPage(),
                'from'         => $resultados->firstItem(),
                'to'           => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items(),
        ]);
    }
    public function SacarCategoria(Request $reques){  /// Sacar Categorias 
        $categorias = ProductosModel::select('categoria')
        ->distinct()
        ->orderBy('categoria', 'asc') 
        ->get();
       return $categorias;
    }
    public function ElementosDeCategoria(Request $request) {
        $categoria = $request->categoriaElemento;
        $talla = $request->TallaElemento;
    
        if ($talla === 'UNICA') {
        
            $productos = ProductosModel::where('categoria', $categoria)
                ->where('estado', 0)
                ->orderBy('codigo', 'asc') 
                ->get();
        } else {
            $productos = ProductosModel::where('categoria', $categoria)
                ->where('talla', $talla)
                ->where('estado', 0) 
                ->orderBy('codigo', 'asc') 
                ->get();
        }
    
        return $productos;
    }
    /// funcones de ganacias 
    public function ganancias (){
        return view ('Administrador/ganancia');
    }
    public function SacarGanancias(Request $request)//// sacar tood las facturas poara contra sus ganancias 
   {
    $datopsFcatura = DB::table('ganancias_diarias') // Nombre de la tabla de ganancias diarias
        ->join('clientes', 'ganancias_diarias.documento', '=', 'clientes.documento') // Suponiendo que 'cliente_id' es la clave foránea en 'ganancias_diarias'
        ->select('ganancias_diarias.*', 'clientes.nombre', 'clientes.documento', 'clientes.telefono', 'clientes.gmail')
        ->get();

    $fecha_inicial = $request->input('startDate');
    $fecha_final = $request->input('endDate');
    $perPage = $request->get('per_page', 10);

    $query = GananciasDIarias::join('clientes', 'ganancias_diarias.documento', '=', 'clientes.documento')
        ->select('ganancias_diarias.*', 'clientes.nombre', 'clientes.documento', 'clientes.telefono', 'clientes.gmail');

    if ($fecha_inicial && $fecha_final) {
        $fecha_inicial = Carbon::createFromFormat('Y-m-d', $fecha_inicial)->startOfDay();
        $fecha_final = Carbon::createFromFormat('Y-m-d', $fecha_final)->endOfDay();
        $query->whereBetween('ganancias_diarias.fecha_de_creacion', [$fecha_inicial, $fecha_final]);
    }
    // Aplicamos el orden antes de obtener los resultados
    $query->orderBy('ganancias_diarias.fecha_de_creacion', 'desc');

    // Obtener los resultados con paginación
    $datosFacturas = $query->paginate($perPage);
    // Total de precios
    $totalPrecio = $query->sum('total_d');

    // Total general de 'datopsFcatura' sin la paginación
    $totalGeneral = $datopsFcatura->sum('total_d');

    
    return response()->json([
        'pagination' => [
            'total'        => $datosFacturas->total(),
            'current_page' => $datosFacturas->currentPage(),
            'per_page'     => $datosFacturas->perPage(),
            'last_page'    => $datosFacturas->lastPage(),
            'from'         => $datosFacturas->firstItem(),
            'to'           => $datosFacturas->lastItem(),
        ],
        'facturas' => $datosFacturas,
        'totalGeneral' => $totalGeneral,
        'totalPrecio'  => $totalPrecio,
        'datopsFcatura' => $datopsFcatura,
    ]);
    }
    public function Roles() {//// sacra roles para hacer validacion
       
        $user = Auth::user();
    
        return response()->json([
            'rol' => $user ? $user->rol : null, 
        ]);
    }
    public function Mostrarsctok(){
        $productosAgrupados = \DB::table('productos')
        ->select('categoria', 'talla', \DB::raw('COUNT(*) as cantidad'))
        ->groupBy('categoria', 'talla')
        ->having('cantidad', '<=', 5)
        ->get();
    
        $totalProductosMenorIgualA5 = $productosAgrupados->count();
    
         return $totalProductosMenorIgualA5;
    

    }   
    
    // Funciones de lociones y su base de datos !!! 
    public function RegistrarLociones(Request $request){
     
        $codigo = $request->codigoLocion;
        $categoria = $request->CategoriaLocion;
        $ml = $request->MlLociones;
        $precio = $request->PrecioLocion;
         
        $Lociones = new lociones();
        $Lociones->codigo = $codigo;
        $Lociones->categoria = $categoria;
        $Lociones->talla = $ml;
        $Lociones->precio = $precio;
        $Lociones->save();


    }
    ///sacra codigo lociones !!!
    public function CodigosLociones(){

      $LocionesDatos = Lociones::all()->pluck('codigo');
      return $LocionesDatos;

    }
    public function AgrgarLociones(Request $request){
        $SelecioneCodigo = $request->SelecioneCodigo;
        $NuevoML = $request->NuevoML;
        $PrecioNv = $request->PrecioNv;

        $locion = Lociones::where('codigo', $SelecioneCodigo)->first();

        if ($locion) {
            $totalPrecio = $locion->precio + $PrecioNv;
            $ml = $locion->talla + $NuevoML;
    
            $locion->precio = $totalPrecio;
            $locion->talla = $ml;
            $locion->save();
        } else {
            // Si no se encuentra la loción, manejar el error.
            return response()->json(['message' => 'Lotion not found'], 404);
        }
    }


        }
    
    
    

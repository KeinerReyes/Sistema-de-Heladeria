<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ProductosModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductoImport;

class InventarioController extends Controller
{
    public function Inventario(){
        return view ('Administrador/Inventario');
    }

    public function registrar()
    {
        return view('Administrador/RegistrarProducto');
    }
    
    public function Agregar()
    {
        return view('Administrador/Agregar');
    }


    public function obtenerProducto($id_productos)
    {
        $producto = ProductosModel::find($id_productos);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    public function actualizarProducto(Request $request, $id_productos)
    {
        $producto = ProductosModel::find($id_productos);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validacion = $request->validate([
            'categoria' => 'required|string|max:255',
            'marca' => 'required|string|max:355',
            'talla' => 'required|string|max:50',
            'referencia' => 'required|string|max:10000',
            'color' => 'required|string|max:200',
            'precio' => 'required|numeric|min:0',
        ]);
        

        $producto->categoria = $validacion['categoria'];
        $producto->marca = $validacion['marca'];
        $producto->talla = $validacion['talla'];
        $producto->referencia = $validacion['referencia'];
        $producto->color = $validacion['color'];
        $producto->precio = $validacion['precio'];
        $producto->save();

        return response()->json(['message' => 'Producto actualizado correctamente'], 200);
    }
    public function listarinv(Request $request) {
        $inventarios = ProductosModel::where('estado', 0)->get();
        return $inventarios;
    }

    public function exel(Request $request){
        
        $file = $request->file('file');
        Excel::import(new ProductoImport, $file);

        return redirect()->route('Inventario')->with('success', 'Importado Correctamente');
    }

    public function obtenerCategorias(){
        $categorias = ProductosModel::select('categoria')->distinct()->get();
        return $categorias;
    }

    public function referencias(){
        $referencias = ProductosModel::select('referencia')->distinct()->get();
        return $referencias;
    }

    public function Registrarproducto(Request $request)
    {
        \Log::info('Datos recibidos:', $request->all());

        $validacion = $request->validate([
            'codigo' => 'required|integer|max:100000000000',
            'categoria' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'talla' => 'required|string|max:50',
            'referencia' => 'required|string|max:100000',
            'color' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
        ]);
    
        $producto = new ProductosModel([
            'codigo' => $validacion['codigo'],
            'categoria' => $validacion['categoria'],
            'marca' => $validacion['marca'],
            'talla' => $validacion['talla'],
            'referencia' => $validacion['referencia'],
            'color' => $validacion['color'],
            'precio' => $validacion['precio'],
        ]);
        $producto->save();

        return response()->json(['message' => 'Producto registrado correctamente'], 201);
    }

    public function Eliminar($id_productos)
    {
            $producto = ProductosModel::find($id_productos);
    
            if (!$producto) {
                return response()->json([
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $producto->delete();

    
    }

        public function agregarCategoriasUnicas(Request $request)
    {

        $validacion = $request->validate([
            'categorias' => 'required|array',
            'categorias.*.categoria' => 'required|string|max:255',
            'categorias.*.referencia' => 'required|string|max:255',
            'categorias.*.talla' => 'required|string|max:50',
        ]);

        $categorias = $validacion['categorias'];

        foreach ($categorias as $categoriaData) {
            $categoriaExistente = ProductosModel::where('categoria', $categoriaData['categoria'])
                                        ->where('referencia', $categoriaData['referencia'])
                                        ->where('talla', $categoriaData['talla'])
                                        ->first();

            if (!$categoriaExistente) {
                // Crear una nueva categoría si no existe
                ProductosModel::create([
                    'categoria' => $categoriaData['categoria'],
                    'referencia' => $categoriaData['referencia'],
                    'talla' => $categoriaData['talla'],
                ]);
            }
        }

        return response()->json(['message' => 'Categorías agregadas correctamente'], 201);
    }
    public function obtenerproductoreferencia(Request $request)
    {
        $validacion = $request->validate([
            'referencia' => 'required|string|max:100000000000000000',
        ]);

        $producto = ProductosModel::where('referencia', $validacion['referencia'])->first();

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }
    public function cantidadProducto(Request $request)
    {
        $referencia = $request->input('referencia');
        $cantidad = ProductosModel::where('referencia', $referencia)->count();
        return response()->json(['cantidad' => $cantidad]);
    }
    public function actualizarTalla(Request $request, $codigo)
    {
        $producto = ProductosModel::where('codigo', $codigo)->first();
    
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    
        $validacion = $request->validate([
            'talla' => 'required|string|max:100',
        ]);
    
        $producto->talla = $validacion['talla'];
        $producto->save();
        return response()->json(['message' => 'Talla actualizada correctamente'], 200);
    }

// ProductoController.php

public function agregarProductos(Request $request)
{
    $validated = $request->validate([
        'producto' => 'required|array',
        'cantidad' => 'required|integer',
    ]);



    try {
        $productoData = $validated['producto'];
        $cantidad = $validated['cantidad'];
      
        $producto = ProductosModel::find($productoData['id_productos']);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $nuevosProductos = []; 

        // Agregar productos
        for ($i = 0; $i < $cantidad; $i++) {
            $nuevoProducto = $producto->replicate();
            $ultimoProducto = ProductosModel::orderBy('codigo', 'desc')->first();
            $nuevoCodigo = $ultimoProducto ? $ultimoProducto->codigo + 1 : 1;
            $nuevoProducto->codigo = $nuevoCodigo;
            $nuevoProducto->save();

            $nuevosProductos[] = $nuevoProducto; 
        }

      
        return response()->json(['message' => 'Productos agregados exitosamente', 'productos' => $nuevosProductos], 200);

    } catch (\Exception $e) {
        \Log::error('Error al agregar productos: ' . $e->getMessage());
        return response()->json(['message' => 'Error al agregar productos'], 500);
    }
}

/// funcones del mudlo de ventas

    public function Ventas(){
        return view ('Administrador/Ventas');
    }
    public function SacraVentas(Request $request) {
        $buscar = $request->input('Buscar');
    
        // Obtener los inventarios con el estado 1, ordenados por la fecha de venta de mayor a menor
        $inventarios = ProductosModel::where('estado', 1)
            ->when($buscar, function ($query) use ($buscar) {
                return $query->where('codigo', 'LIKE', "%$buscar%");
            })
            ->orderBy('fecha_de_creacion', 'desc')  // Ordenar por fecha de venta de mayor a menor
            ->paginate(14)
            ->appends(['Buscar' => $buscar]); 
    
        return response()->json([
            'data' => $inventarios->items(),
            'pagination' => [
                'total'        => $inventarios->total(),
                'current_page' => $inventarios->currentPage(),
                'per_page'     => $inventarios->perPage(),
                'last_page'    => $inventarios->lastPage(),
                'from'         => $inventarios->firstItem(),
                'to'           => $inventarios->lastItem(),
            ],
        ]);
    }
         
    

    //// una funcion  de resgitrat porducto es sobre las talla apar tener mejor control sobre ella 
    public function buscarTallas(Request $request)
    {

        $query = $request->input('query');

        $tallas = ProductosModel::select('talla')
                          ->where('talla', 'like', "%$query%")
                          ->groupBy('talla')  
                          ->get();

        return response()->json($tallas);
    }



}

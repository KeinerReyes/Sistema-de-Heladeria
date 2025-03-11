<?php

namespace App\Http\Controllers;

use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use App\Models\ProductosModel;
use App\Models\Clientes;
use App\Models\Factura;
use App\Models\vendidos;
use App\Models\Lociones;
use App\Models\GananciasDIarias;
use Illuminate\Support\Facades\DB;
class FacturaController extends Controller
{
    public function FacturaIndex (){
            return view ('Administrador/Factura');
    }

    public function BusquedaCodigo(Request $request){
        //Busca codigo del producto que seleciono 
        $codigo = $request->query('codigo');
        $producto = ProductosModel::where('codigo', $codigo)
                                  ->where('estado', 0)  // Condición para el estado
                                  ->first();
        return response()->json($producto);
    }

    public function GuardasClientes(Request $request) { /// Guarda datos del clientes !!! 
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|digits:10',
            'fechaNacimiento' => 'required|date',
            'gmail' => 'required|email'
        ]);
        $usuario = new Clientes();
        $usuario->nombre = $request->nombre;
        $usuario->telefono = $request->telefono;
        $usuario->documento = $request->documento;
        $usuario->fecha_nacimiento = $request->fechaNacimiento;
        $usuario->gmail = $request->gmail;
        $usuario->save();

        return response()->json([
            'message' => 'Cliente creado exitosamente.',
        ]);
    }
    public function ListraUsuarios(Request $request)  // Lista usuarios y busca usuarios 
    {
        $usuario = $request->input('BusquedaDocumentoC');
        $clientes = Clientes::where('documento', 'like', "%{$usuario}%")->get();
        return response()->json($clientes);
    }

    public function RalizarFacturaBusqueda(Request $request){  /// Busqueda del usuaio completo por su documento para poder realizar su factura

        $documento = $request->documento; 
        $Datos = Clientes::where('documento',$documento)->get();
        return response()->json($Datos);

    }

    public function Factura(Request $request){  ///Guarda todo lo que se le realizo al quiente!!!!
        $DatosClientes = $request->DatosClientes[0]; 
        $AgregarPruducto = $request->AgregarPruducto; 
        $devolucion = $request->devolucion;
        /// recojer los datos de las lociones !!! 

        $Total = $request->Total; 
        $EstadoConSIste = $request->EstadoConSIste; 
        $descuentoPorcentaje = $request->descuentoPorcentaje;
        $descuentoValor = $request->descuentoValor;
        $mp = $request->mp;
        if ($request->mp == 3 || $request->mp == 4) {
            $abono = $request->abono;
        }
        if ($request->mp == 6) {
            $devolucion = 0;
            if ($request->devolucion != 0) {
                $devolucion = $request->devolucion;
            }
        }

        ////Funciones para guardar el total de ganancias del dia diario donde se ve el abono y tood 
        $GananciasD = new GananciasDIarias();
        if ($request->mp == 1) {
            $GananciasD->total_d = $Total;
            $GananciasD->mp = $request->mp;
            $GananciasD->documento = $DatosClientes['documento'];
        } 
        if ($request->mp == 2) {
            $GananciasD->total_d = $Total;
            $GananciasD->mp = $request->mp;
            $GananciasD->documento = $DatosClientes['documento'];
        } 
        if ($request->mp == 3) {
            $GananciasD->total_d = $request->abono;
            $GananciasD->mp = $request->mp;
            $GananciasD->documento = $DatosClientes['documento'];
        } 
        if ($request->mp == 4) {
            $GananciasD->total_d = $request->abono;
            $GananciasD->mp = $request->mp;
            $GananciasD->documento = $DatosClientes['documento'];
        } 
        if ($request->mp == 6) {
            $GananciasD->total_d = $devolucion;
            $GananciasD->mp = $request->mp;
            $GananciasD->documento = $DatosClientes['documento'];
        } 
        $GananciasD->save();

        $factura = new Factura();
        $factura->nombre = $DatosClientes['nombre'];
        $factura->documento = $DatosClientes['documento'];
        $factura->gmail = $DatosClientes['gmail'];
        $factura->telefono = $DatosClientes['telefono'];
        $factura->descripcion = json_encode($AgregarPruducto); // Guardar el JSON de productos
        if($request->mp == 6){
            $factura->totalF = $devolucion;
        }else{
            $factura->totalF = $Total;
        }
       
        $factura->mp = $mp;

        if ($request->mp == 3 || $request->mp == 4) {
        $factura->debe = $Total - $abono;
        } 

        $factura->estado = $EstadoConSIste;
        if (!empty($descuentoPorcentaje)) {
            $factura->descuento = $descuentoPorcentaje; 
        } elseif (!empty($descuentoValor)) {
            $factura->descuento = $descuentoValor; 
        } else {
            $factura->descuento = 0; 
        }

        $factura->save();
    
     
        $AgregarPruducto = $request->AgregarPruducto; 
        if (is_array($AgregarPruducto)) {
            foreach ($AgregarPruducto as $producto) {
                $id_productos[] = $producto['id_productos'];
            }
        } else {

            $id_productos = $AgregarPruducto['id_productos'];
        }
             $id_productos = array_column($AgregarPruducto, 'id_productos');

            \DB::table('productos')
            ->whereIn('id_productos', $id_productos)
            ->update(['estado' => 1]); // Cambiar 'estado' a 1


          

            $filteredData = collect($AgregarPruducto)->filter(function($item) {
                return isset($item['pr']) && $item['pr'] == 1;
            });
            $filteredData = $filteredData->map(function($item) {
                $item['pr'] = 1; // Establecer 'activo' a 1
                return $item;
            });
            $mlsRestar = $request->mlsRestar; 
            $preciorestar = $request->preciorestar; 
            foreach ($filteredData as $data) {
                Lociones::where('codigo', $data['codigo'])
                    ->update([
                        'talla' => DB::raw('talla - ' . $mlsRestar),
                        'precio' => DB::raw('precio - ' . $preciorestar),
                    ]);
            }
           
    }

    public function SacarOrdenServicio(){
     
        $orden = Factura::orderBy('orden_servicio', 'desc')->first();

        $ultimaOrden = $orden ? $orden->orden_servicio + 1 : 1; 
 
        return $ultimaOrden;
    }

    public function BuscarCodigoLocion(Request $request){

     $codigo = $request->Perfume; 

     $lociones = Lociones::where('codigo', $codigo)->first();

     return $lociones;


    }
    
    
}

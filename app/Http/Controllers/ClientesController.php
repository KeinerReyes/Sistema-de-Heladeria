<?php

namespace App\Http\Controllers;
use App\Models\Clientes;
use App\Models\Factura;
use App\Models\GananciasDIarias;
use Illuminate\Http\Request;
use App\Mail\EnviarCorreo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
class ClientesController extends Controller
{
    public function clientes()
    {
        return view('Administrador/clientes');
    }
    public function listarProductosApartados(Request $request){
        $documento = $request->documento;

        $facturas = Factura::where('documento', $documento)
        ->where('mp', 4)
        ->pluck('descripcion'); 


        
        $descripcionArray = $facturas->map(function ($descripcion) {
            return json_decode($descripcion); 
        })->flatten(); 
        
        $finalArray = $descripcionArray->toArray();
        $totalFacturas = Factura::where('documento', $documento)
        ->where('mp', 4)
        ->sum('debe');


        return [
            'productos' => $finalArray,
            'total' => $totalFacturas,
            'documento' =>  $documento
        ];

    }
    public function AbonarProductoApartado(Request $request)
    {
        $Abono = $request->Abono;
        $documento = $request->documento;
    
        $GananciasD = new GananciasDIarias();
        $GananciasD->total_d = $Abono;
        $GananciasD->documento = $documento;
        $GananciasD->mp = 5;
        $GananciasD->save();

        $facturas = Factura::where('documento', $documento)
            ->where('mp', 4)
            ->get();
        
        foreach ($facturas as $factura) {
            if ($Abono <= 0) {
                break;
            }
            
            $debeActual = $factura->debe;
            if ($debeActual > 0) {
                $nuevoSaldo = max(0, $debeActual - $Abono);
                $abonoAplicado = $debeActual - $nuevoSaldo;
                $factura->debe = $nuevoSaldo;
                if ($nuevoSaldo == 0) {
                    $factura->mp = 5;
                }
                $factura->save();
                $Abono -= $abonoAplicado;
            }
        }
        
        if ($Abono > 0) {
            return response()->json(['error' => 'El abono no se pudo aplicar completamente'], 400);
        }
        
        // Respuesta exitosa
        return response()->json(['success' => 'Abono aplicado correctamente'], 200);
    }
    /// pago total del apartado 
    public function PagarCompletoApartado(Request $request){
        $documento = $request->documento;
        $facturas = DB::table('factura') 
        ->where('documento', $documento)
        ->where('mp', 4)
        ->get();
        $totalDeuda = 0;

        foreach ($facturas as $factura) {
            $totalDeuda += $factura->debe;
        }

        $totalDeudaVariable = $totalDeuda;

        $GananciasD = new GananciasDIarias();
        $GananciasD->total_d = $totalDeudaVariable;
        $GananciasD->documento = $documento;
        $GananciasD->mp = 5;
        $GananciasD->save();

        $updatedRows = DB::table('factura')
        ->where('documento', $documento)
        ->where('mp', 4)
        ->update(['debe' => 0,'mp' => 5]);

        if ($updatedRows > 0) {
            return response()->json([
                'success' => 'exito'
            ]);
        }
    }
    public function listar(Request $request)
     {
         $query = $request->input('ClienteBusqueda'); // Obtén el parámetro de búsqueda
         
         // Si hay un término de búsqueda, filtra los clientes; si no, trae todos
         $clientes = Clientes::when($query, function ($queryBuilder) use ($query) {
             $queryBuilder->where('nombre', 'like', '%' . $query . '%')
                          ->orWhere('documento', 'like', '%' . $query . '%');
         })->get();
         
         // Procesa la información adicional para los clientes
         $clientesConDeuda = $clientes->map(function ($cliente) {
             $cliente->deuda = Factura::where('documento', $cliente->documento)->sum('debe'); // Suma de la deuda
             $cliente->mpValores = Factura::where('documento', $cliente->documento)->pluck('mp'); // Valores de 'mp'
             return $cliente;
         });
     
         return response()->json($clientesConDeuda);
    }
    public function  listarCredito(Request $request){

        $documentos = Clientes::pluck('documento');
        $deudas = []; 
        foreach ($documentos as $documento) {
            $deuda = Factura::where('documento', $documento)->sum('debe');
            $deudas[$documento] = $deuda;
        }
    
        return $deudas;
    }
    public function actualizarCliente(Request $request, $id_cliente)
    {
        $cliente = Clientes::find($id_cliente);
    
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    
        $validacion = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|numeric|min:1000000|max:999999999999', 
            'telefono' => 'required|numeric|digits_between:7,15',
            'gmail' => 'required|email|max:255',
            'fecha_nacimiento' => 'required|date',
        ]);
        $cliente->nombre = $validacion['nombre'];
        $cliente->documento = $validacion['documento'];
        $cliente->telefono = $validacion['telefono'];
        $cliente->gmail = $validacion['gmail'];
        $cliente->fecha_nacimiento = $validacion['fecha_nacimiento'];
        
        $cliente->save();
    
        return response()->json(['message' => 'Cliente actualizado correctamente'], 200);
    }
    public function eliminarCliente($id_cliente)
    {
            $cliente = Clientes::find($id_cliente);
    
            if (!$cliente) {
                return response()->json([
                    'message' => 'cliente no encontrado'
                ], 404);
            }

            $cliente->delete();

    
    }
    public function obtenerCumpleaneros(){
        $hoy = date('m-d'); 
        $cumpleaneros = Clientes::whereRaw('DATE_FORMAT(fecha_nacimiento, "%m-%d") = ?', [$hoy])->get();
        
        return response()->json($cumpleaneros);
    }
    public function buscarCliente(Request $request)
        {
            $query = $request->input('ClienteBusqueda');
            dd($query);
            // Busca en nombre o documento
            $clientes = Clientes::where('nombre', 'like', '%' . $query . '%')
                ->orWhere('documento', 'like', '%' . $query . '%')
                ->get();

            return response()->json($clientes);
    }
    public function enviarCorreo(Request $request)
        {
            $destinatarios = $request->destinatarios;  // Lista de destinatarios
             $mensaje = $request->mensaje;              // Mensaje a enviar

            // Enviar el correo a cada destinatario
            foreach ($destinatarios as $destinatario) {
            Mail::to($destinatario)->send(new EnviarCorreo($mensaje));        
            }

        return response()->json(['mensaje' => 'Correos enviados correctamente']);

    }
    public function datosClientesAbono(Request $request){
        $id_cliente = $request->id_cliente; 
        
        $cliente = Clientes::select('documento', 'nombre')->where('id_cliente', $id_cliente)->first();
        if ($cliente) {
            $sumaDebe = Factura::where('documento', $cliente->documento)
            ->where('mp', 3)
            ->sum('debe');

            return response()->json([
                'documento' => $cliente->documento,
                'nombre' => $cliente->nombre,
                'suma_debe' => $sumaDebe
            ]);

    }
    }
    public function Abono(Request $request){
        $Abono = $request->Abono;
        $documento = $request->documentoAbono;

        $GananciasD = new GananciasDIarias();
        $GananciasD->total_d = $Abono;
        $GananciasD->documento = $documento;
        $GananciasD->mp = 3;
        $GananciasD->save();

        // Obtener todas las facturas del cliente
        $facturas = Factura::where('documento', $documento)
        ->where('mp', 3)
        ->get();
        
        foreach ($facturas as $factura) {
            // Verificar si el abono aún tiene valor
            if ($Abono <= 0) {
                break; // Si no hay abono restante, salir del bucle
            }
        
            // Calcular cuánto se puede abonar a esta factura
            $debeActual = $factura->debe;
            if ($debeActual > 0) {
                $nuevoSaldo = max(0, $debeActual - $Abono);
                $abonoAplicado = $debeActual - $nuevoSaldo;
                $factura->debe = $nuevoSaldo;
                $factura->save(); 
                $Abono -= $abonoAplicado;
            }
        }
        
        // Si el abono se ha agotado, no hacer nada más
        if ($Abono > 0) {
            return response()->json(['error' => 'El abono no se pudo aplicar completamente'], 400);
        }
        
        // Respuesta exitosa
        return response()->json(['success' => 'Abono aplicado correctamente'], 200);
        
    }
    public function AbonoTotal(Request $request){
        $documento = $request->documentoAbono;

        $facturas = DB::table('factura') 
        ->where('documento', $documento)
        ->get();
        $totalDeuda = 0;
        foreach ($facturas as $factura) {
            $totalDeuda += $factura->debe;
        }

        $totalDeudaVariable = $totalDeuda;

        $GananciasD = new GananciasDIarias();
        $GananciasD->total_d = $totalDeudaVariable;
        $GananciasD->documento = $documento;
        $GananciasD->mp = 3;
        $GananciasD->save();


        $updatedRows = DB::table('factura')
        ->where('documento', $documento)
        ->where('mp', 3)
        ->update(['debe' => 0]);

        if ($updatedRows > 0) {
            return response()->json([
                'success' => 'Todas las facturas del cliente han sido abonadas exitosamente.'
            ]);
        }
    }

    public function SacarDetallesAbono(Request $request){
        $documento = $request->documento;
        $resultados = DB::table('ganancias_diarias as gd')
        ->join('clientes as a', 'gd.documento', '=', 'a.documento')
        ->select('gd.*', 'a.nombre')
        ->where('gd.documento', $documento)
        ->whereIn('gd.mp', [3,4, 5])  // Filtramos los abonos que sean 3 o 5
        ->get();
    return response()->json($resultados);
    }

}
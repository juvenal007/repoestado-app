<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Estado;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoController extends Controller
{
    public function recibido(Request $request)
    {
        try {
            // BUSCAMOS LOS DOCUMENTOS QUE TENGAN EL DOCU_CODIGO EN REGLA SOLO TRAERA UN UNICO ELEMENTO
            $documento = Documento::where('docu_codigo', $request->data['docu_codigo'])->first();

            // BUSCAMOS EL ULTIMO ESTADO DEL DOCUMENTO, YA SEA RECIBIDO O ENVIADO    
            // OBTENEMOS TODOS LOS ESTADOS DEL DOCUMENTO, LUEGO ORDENAMOS POR ID DESCENDIENTE Y OBTENEMOS EL PRIMER VALOR
            // QUE EN REGLA SERÍA EL ÚLTIMO REGISTRO DE ESTADO.   
            $ultimoEstado = Estado::where('documento_id', $documento->id)->orderBy('estado_fecha_ingreso', 'DESC')->get()->first();

            // VARIABLES GENERALES
            $fecha = date("Y-m-d H:i:s");
            $user = json_decode($request->data['usuario']);

            // ANUNCIAMOS LA PARTIDA PARA NUESTRO ROLL BACK
            DB::beginTransaction();

            //MODIFICAMOS EL REGISTRO DE ESTADO FECHA PARA FINALIZAR SU PROCESO   

            if ($ultimoEstado->estado_nombre == 'ENVIADO') {
                $ultimoEstado->estado_fecha_egreso = $fecha;
                $ultimoEstado->save();
            } else if ($ultimoEstado->estado_nombre == 'TERMINADO') {
                return response()->json(['response' => ['status' => true, 'data' => $ultimoEstado, 'message' => 'DOCUMENTO YA TERMINADO']], 501);
            } else {
                $ultimoEstado->estado_nombre = 'ACEPTADO';
                $ultimoEstado->estado_fecha_egreso = $fecha;
                $ultimoEstado->save();
            }


            // CREAMOS LA DATA PARA AGREGAR EL ULTIMO ESTADO DEL DOCUMENTO.

            $data = [
                'estado_nombre' => 'RECIBIDO',
                'estado_descripcion' => 'Recibido por ' . $user->usuario_nombre,
                'estado_fecha_ingreso' => $fecha,
                'estado_fecha_egreso' => null,
                'usuario_id' => $user->id,
                'departamento_id' => $user->departamento_id,
                'documento_id' => $documento->id
            ];

            $estado = new Estado($data);
            $estado->save();


            DB::commit();
            return response()->json(['response' => ['status' => true, 'data' => $estado, 'message' => 'Centro de Costos Actualizado']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function terminado(Request $request)
    {
        try {
            // BUSCAMOS LOS DOCUMENTOS QUE TENGAN EL DOCU_CODIGO EN REGLA SOLO TRAERA UN UNICO ELEMENTO
            $documento = Documento::where('docu_codigo', $request->data['docu_codigo'])->first();

            // BUSCAMOS EL ULTIMO ESTADO DEL DOCUMENTO, YA SEA RECIBIDO O ENVIADO    
            // OBTENEMOS TODOS LOS ESTADOS DEL DOCUMENTO, LUEGO ORDENAMOS POR ID DESCENDIENTE Y OBTENEMOS EL PRIMER VALOR
            // QUE EN REGLA SERÍA EL ÚLTIMO REGISTRO DE ESTADO.   
            $ultimoEstado = Estado::where('documento_id', $documento->id)->orderBy('estado_fecha_ingreso', 'DESC')->get()->first();

            // VARIABLES GENERALES
            $fecha = date("Y-m-d H:i:s");
            $user = json_decode($request->data['usuario']);

         

            //MODIFICAMOS EL REGISTRO DE ESTADO FECHA PARA FINALIZAR SU PROCESO              
            if ($ultimoEstado->estado_nombre == 'TERMINADO') {
                return response()->json(['response' => ['status' => true, 'data' => $ultimoEstado, 'message' => 'DOCUMENTO YA TERMINADO']], 200);
            } else {
                $ultimoEstado->estado_nombre = 'TERMINADO';
                $ultimoEstado->estado_fecha_egreso = $fecha;
                $ultimoEstado->save();
            }

           // ACTUALIZAMOS EL DOCUMENTO A SU ESTADO TERMINADO
           $documento->docu_estado = 'TERMINADO';
           $documento->save();

          
        } catch (\Illuminate\Database\QueryException $e) {
       
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

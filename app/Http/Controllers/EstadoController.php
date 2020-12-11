<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Estado;
use Carbon\Carbon;
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

            if ($documento == null) {
                return response()->json(['response' => ['status' => false, 'data' => $documento, 'message' => 'Codigo Incorrecto']], 201);
            } else if ($documento->docu_estado == 'TERMINADO') {
                return response()->json(['response' => ['status' => false, 'data' => $documento, 'message' => 'Documento Terminado']], 201);
            }



            // BUSCAMOS EL ULTIMO ESTADO DEL DOCUMENTO, YA SEA RECIBIDO O ENVIADO
            // OBTENEMOS TODOS LOS ESTADOS DEL DOCUMENTO, LUEGO ORDENAMOS POR ID DESCENDIENTE Y OBTENEMOS EL PRIMER VALOR
            // QUE EN REGLA SERÍA EL ÚLTIMO REGISTRO DE ESTADO.
            $ultimoEstado = Estado::where('documento_id', $documento->id)->orderBy('estado_fecha_ingreso', 'DESC')->first();

            // VARIABLES GENERALES
            $fecha = date("Y-m-d H:i:s");
            $user = json_decode($request->data['usuario']);

            // ANUNCIAMOS LA PARTIDA PARA NUESTRO ROLL BACK


            //MODIFICAMOS EL REGISTRO DE ESTADO FECHA PARA FINALIZAR SU PROCESO

            if ($ultimoEstado->estado_nombre == 'ENVIADO') {
                $ultimoEstado->estado_fecha_egreso = $fecha;
                $ultimoEstado->save();
            } else if ($ultimoEstado->estado_nombre == 'TERMINADO') {
                return response()->json(['response' => ['status' => true, 'data' => $ultimoEstado, 'message' => 'Documento terminado']], 200);
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



            return response()->json(['response' => ['status' => true, 'data' => $estado, 'message' => 'Documento Recibido']], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 200);
        }
    }

    public function terminado(Request $request)
    {
        try {
            // BUSCAMOS LOS DOCUMENTOS QUE TENGAN EL DOCU_CODIGO EN REGLA SOLO TRAERA UN UNICO ELEMENTO
            $documento = Documento::where('docu_codigo', $request->data['docu_codigo'])->first();
            if ($documento == null) {
                return response()->json(['response' => ['status' => false, 'data' => $documento, 'message' => 'Codigo Incorrecto']], 201);
            } else if ($documento->docu_estado == 'TERMINADO') {
                return response()->json(['response' => ['status' => false, 'data' => $documento, 'message' => 'El documento ya ha sido terminado']], 201);
            }
            // BUSCAMOS EL ULTIMO ESTADO DEL DOCUMENTO, YA SEA RECIBIDO O ENVIADO
            // OBTENEMOS TODOS LOS ESTADOS DEL DOCUMENTO, LUEGO ORDENAMOS POR ID DESCENDIENTE Y OBTENEMOS EL PRIMER VALOR
            // QUE EN REGLA SERÍA EL ÚLTIMO REGISTRO DE ESTADO.
            $ultimoEstado = Estado::where('documento_id', $documento->id)->orderBy('estado_fecha_ingreso', 'DESC')->get()->first();

            // VARIABLES GENERALES
            $fecha = date("Y-m-d H:i:s");
            $user = json_decode($request->data['usuario']);



            //MODIFICAMOS EL REGISTRO DE ESTADO FECHA PARA FINALIZAR SU PROCESO
            if ($ultimoEstado->estado_nombre == 'TERMINADO') {
                return response()->json(['response' => ['status' => true, 'data' => $ultimoEstado, 'message' => 'El documento ya ha sido terminado']], 200);
            } else {
                $ultimoEstado->estado_nombre = 'TERMINADO';
                $ultimoEstado->estado_fecha_egreso = $fecha;
                $ultimoEstado->save();
            }

            // ACTUALIZAMOS EL DOCUMENTO A SU ESTADO TERMINADO
            $documento->docu_estado = 'TERMINADO';
            $documento->docu_fecha_egreso = $fecha;
            $documento->save();
            return response()->json(['response' => ['status' => true, 'data' => $documento, 'message' => 'Documento Terminado con exito.']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function list_documento($documento_id)
    {
        try {
            $estados = Estado::with('usuario', 'departamento', 'documento')->where('documento_id', $documento_id)->get()->all();
            $documento = Documento::with('tipo_documento')->where('id', $documento_id)->get()->first();



            foreach ($estados as $clave => $valor) {

                if (!$valor->estado_fecha_egreso == null) {
                    $fechaIngreso = Carbon::parse($valor->estado_fecha_ingreso)->format('Y-m-d H:i:s');
                    $fechaIngreso = Carbon::create($fechaIngreso);

                    $fechaEgreso = Carbon::parse($valor->estado_fecha_egreso)->format('Y-m-d H:i:s');
                    $fechaEgreso = Carbon::create($fechaEgreso);

                    $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                    $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                    $estados[$clave]->diferenciaMeses = $diferenciaMeses;
                    $estados[$clave]->diferenciaDias = $diferenciaDias;
                    $estados[$clave]->diferenciaHoras = $diferenciaHoras;
                    $estados[$clave]->diferenciaMinutos = $diferenciaMinutos;
                }


                $estados[$clave]->estado_fecha_ingreso = Carbon::parse($valor->estado_fecha_ingreso)->format('d-m-Y H:i');
            }




            return response()->json(['response' => ['status' => true, 'data' => ['estados' => $estados, 'documento' => $documento], 'message' => 'LISTA DE ESTADOS']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

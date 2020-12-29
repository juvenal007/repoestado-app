<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PdfController;
use App\Models\Archivo;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use Carbon\Carbon;
use DateTimeZone;

class DocumentoController extends Controller
{
    public function add(Request $request)
    {
        try {
            $subirArchivo = FileUploadController::do_upload($request);

            $ultimoDocumentoId = Documento::all()->count();

            $letrasCodigo = substr($subirArchivo['tipo_documento'], 0, 2);
            $user = json_decode($subirArchivo['user']);
            $destinatarios = json_decode($subirArchivo['destinatarios']);

            // SI ES LA PRIMERA VEZ QUE CREAN UN ARCHIVO LE ASIGNAMOS EL NUMERO 1
            if ($ultimoDocumentoId == 0) {
                $datos = [
                    'docu_codigo' => $letrasCodigo . '-' . crc32(1),
                    'docu_nombre' => $subirArchivo['nombre_documento'],
                    'docu_estado' => 'ENVIADO',
                    'docu_descripcion' => $subirArchivo['descripcion_documento'],
                    'usuario_id' => $user->id,
                    'tipo_documento_id' => (int) $subirArchivo['id_documento'],
                ];
            }

            $datos = [
                'docu_codigo' => $letrasCodigo . '-' . crc32($ultimoDocumentoId),
                'docu_nombre' => $subirArchivo['nombre_documento'],
                'docu_estado' => 'ENVIADO',
                'docu_descripcion' => $subirArchivo['descripcion_documento'],
                'usuario_id' => $user->id,
                'departamento_id' => $user->departamento_id,
                'tipo_documento_id' => (int) $subirArchivo['id_documento'],
            ];
            // ANUNCIAMOS LA PARTIDA PARA NUESTRO ROLL BACK
            DB::beginTransaction();


            // CREAMOS EL DOCUMENTO
            $documento = new Documento($datos);
            $documento->save();

            // CREAMOS LOS DATOS DE TABLA DE ARCHIVO

            $datos = [
                'archivo_nombre' => $subirArchivo['nombre_archivo'],
                'archivo_extension' => $subirArchivo['extension'],
                'archivo_url' => $subirArchivo['file'],
                'archivo_descripcion' => 'DOCUMENTO',
                'documento_id' => $documento->id,
            ];

            $archivo = new Archivo($datos);
            $archivo->save();

            // CREAMOS EL ESTADO INICIAL DEL DOCUMENTO EN LA TABLA ESTADOS
            // VARIABLES FIJAS DE ESTADO
            $fecha = date("Y-m-d-H-i-s");

            $datos = [
                'estado_nombre' => 'ENVIADO',
                'estado_descripcion' => 'Enviado por ' . $user->usuario_nombre,
                'estado_fecha_ingreso' => $fecha,
                'estado_fecha_egreso' => null,
                'usuario_id' => $user->id,
                'departamento_id' => $user->departamento_id,
                'documento_id' => $documento->id
            ];

            $estado = new Estado($datos);
            $estado->save();

            // CREAMOS LA TABLA ESTADO EN PROCESO Y ENVIAMOS A LOS DESTINATARIOS SI CORRESPONDE


            if (sizeof($destinatarios) > 0) {
                foreach ($destinatarios as $index => $valores) {
                    $datos = [
                        'estado_nombre' => 'EN PROCESO',
                        'estado_descripcion' => 'Archivo enviado a ' . $valores->nombre,
                        'estado_fecha_ingreso' => $fecha,
                        'estado_fecha_egreso' => null,
                        'usuario_id' => $valores->id,
                        'departamento_id' => $valores->departamento_id,
                        'documento_id' => $documento->id
                    ];
                    $estado = new Estado($datos);
                    $estado->save();
                }
            }

            //SI TODO SALE BIEN, GENERAMOS EL PDF FOLIO

            $user = Usuario::with('departamento')->where('id', $user->departamento_id)->get();
            $documento = Documento::with('tipo_documento')->where('id', $documento->id)->get();

            $data = [
                'user' => $user[0],
                'documento' => $documento[0],
                'archivo' => $archivo,
            ];
            //GENERAMOS EL PDF
            $folio = PdfController::generarPdf($data);

            // GUARDAMOS EL PDF GENERADO EN LA TABLA ARCHIVOS.
            $archivo = new Archivo($folio);
            $archivo->save();
            $archivo['url_completa'] = URL::to('/') . "/" . $archivo['archivo_url'];



            //GUARDAMOS EN LA BASE DE DATOS SI TODO SALE BIEN.
            DB::commit();
            return response()->json(['response' => ['status' => true, 'data' => $archivo, 'message' => 'Documento Ingresado con Exito']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // SI ALGO SALE MAL HACEMOS ROLLBACK AL INICIO
            DB::rollback();
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function calcularTiempo($documentos_unicos)
    {
        try {
            $fecha = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'));


            foreach ($documentos_unicos as $clave => $valor) {

                $fechaNueva = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');

                if (!$valor->docu_fecha_egreso == null) {

                    $fechaIngreso = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');
                    $fechaIngreso = Carbon::create($fechaIngreso);

                    $fechaEgreso = Carbon::parse($valor->docu_fecha_egreso)->format('Y-m-d H:i:s');
                    $fechaEgreso = Carbon::create($fechaEgreso);

                    $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                    $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;

                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                } else {
                    $fechaCarbon = Carbon::create($fechaNueva);

                    // CALCULO LA DIFERENCIA DE DIAS
                    $diferenciaMeses = $fecha->diffInMonths($fechaCarbon, true);
                    $diferenciaDias = $fecha->diffInDays($fechaCarbon->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fecha->diffInHours($fechaCarbon->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fecha->diffInMinutes($fechaCarbon->addHours($diferenciaHoras), true);
                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;
                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                }
            }
            return $documentos_unicos;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function list_departamento($id_departamento)
    {
        try {

            $documentos = Documento::with('departamento', 'usuario')->where('departamento_id', $id_departamento)->orderBy('id', 'DESC')->get();

            $documentos = $this->calcularTiempo($documentos);

            return response()->json(['response' => ['status' => true, 'data' => $documentos, 'message' => 'Lista Documentos por departamento']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
    public function list_usuario($id_usuario)
    {
        try {

            $fecha = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'));
            $estados = Estado::with('usuario', 'documento')->where('usuario_id', $id_usuario)->select('usuario_id','documento_id')->groupBy('usuario_id', 'documento_id')->orderBy('id', 'DESC')->get()->toArray();
          /*   $estados = Estado::with('usuario', 'documento')->where('usuario_id', $id_usuario)->distinct('documento_id')->orderBy('id', 'DESC')->get(); */
          /*   return response()->json(['response' => ['status' => true, 'data' => $estados, 'message' => $estados]], 200); */
            $documentos_unicos = [];

            foreach ($estados as $clave => $valor) {
                $documentos = Documento::with('usuario')->where('id', $valor['documento']['id'])->first();
                array_push($documentos_unicos, $documentos);

            }

            foreach ($documentos_unicos as $clave => $valor) {

                $fechaNueva = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');

                if (!$valor->docu_fecha_egreso == null) {

                    $fechaIngreso = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');
                    $fechaIngreso = Carbon::create($fechaIngreso);

                    $fechaEgreso = Carbon::parse($valor->docu_fecha_egreso)->format('Y-m-d H:i:s');
                    $fechaEgreso = Carbon::create($fechaEgreso);

                    $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                    $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;

                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                } else {
                    $fechaCarbon = Carbon::create($fechaNueva);

                    // CALCULO LA DIFERENCIA DE DIAS
                    $diferenciaMeses = $fecha->diffInMonths($fechaCarbon, true);
                    $diferenciaDias = $fecha->diffInDays($fechaCarbon->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fecha->diffInHours($fechaCarbon->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fecha->diffInMinutes($fechaCarbon->addHours($diferenciaHoras), true);
                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;
                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                }
            }


            return response()->json(['response' => ['status' => true, 'data' => $documentos_unicos, 'message' => $documentos_unicos]], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function list_usuario_unique($id_usuario)
    {
        try {

            $fecha = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'));

            $documentos_unicos = Documento::with('usuario')->where('usuario_id', $id_usuario)->orderBy('id', 'DESC')->get();


            foreach ($documentos_unicos as $clave => $valor) {

                $fechaNueva = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');

                if (!$valor->docu_fecha_egreso == null) {

                    $fechaIngreso = Carbon::parse($valor->created_at)->format('Y-m-d H:i:s');
                    $fechaIngreso = Carbon::create($fechaIngreso);

                    $fechaEgreso = Carbon::parse($valor->docu_fecha_egreso)->format('Y-m-d H:i:s');
                    $fechaEgreso = Carbon::create($fechaEgreso);

                    $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                    $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;

                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                } else {
                    $fechaCarbon = Carbon::create($fechaNueva);

                    // CALCULO LA DIFERENCIA DE DIAS
                    $diferenciaMeses = $fecha->diffInMonths($fechaCarbon, true);
                    $diferenciaDias = $fecha->diffInDays($fechaCarbon->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fecha->diffInHours($fechaCarbon->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fecha->diffInMinutes($fechaCarbon->addHours($diferenciaHoras), true);
                    $documentos_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $documentos_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $documentos_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $documentos_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;
                    $documentos_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor->created_at)->format('d-m-Y H:i:s');
                }
            }


            return response()->json(['response' => ['status' => true, 'data' => $documentos_unicos, 'message' => 'user']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function details($codigo)
    {
        try {

            $fecha = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'));

            $documentos_unicos = Documento::with('usuario')->where('docu_codigo', $codigo)->first();

            if ($documentos_unicos == null) {
                return response()->json(['response' => ['status' => false, 'data' => $documentos_unicos, 'message' => 'Codigo Incorrecto']], 200);
            }



            $fechaNueva = Carbon::parse($documentos_unicos->created_at)->format('Y-m-d H:i:s');

            if (!$documentos_unicos->docu_fecha_egreso == null) {

                $fechaIngreso = Carbon::parse($documentos_unicos->created_at)->format('Y-m-d H:i:s');
                $fechaIngreso = Carbon::create($fechaIngreso);

                $fechaEgreso = Carbon::parse($documentos_unicos->docu_fecha_egreso)->format('Y-m-d H:i:s');
                $fechaEgreso = Carbon::create($fechaEgreso);

                $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                $documentos_unicos->diferenciaMeses = $diferenciaMeses;
                $documentos_unicos->diferenciaDias = $diferenciaDias;
                $documentos_unicos->diferenciaHoras = $diferenciaHoras;
                $documentos_unicos->diferenciaMinutos = $diferenciaMinutos;

                $documentos_unicos->docu_fecha_ingreso = Carbon::parse($documentos_unicos->created_at)->format('d-m-Y H:i:s');
            } else {
                $fechaCarbon = Carbon::create($fechaNueva);

                // CALCULO LA DIFERENCIA DE DIAS
                $diferenciaMeses = $fecha->diffInMonths($fechaCarbon, true);
                $diferenciaDias = $fecha->diffInDays($fechaCarbon->addMonth($diferenciaMeses), true);
                $diferenciaHoras = $fecha->diffInHours($fechaCarbon->addDays($diferenciaDias), true);
                $diferenciaMinutos = $fecha->diffInMinutes($fechaCarbon->addHours($diferenciaHoras), true);
                $documentos_unicos->diferenciaMeses = $diferenciaMeses;
                $documentos_unicos->diferenciaDias = $diferenciaDias;
                $documentos_unicos->diferenciaHoras = $diferenciaHoras;
                $documentos_unicos->diferenciaMinutos = $diferenciaMinutos;
                $documentos_unicos->docu_fecha_ingreso = Carbon::parse($documentos_unicos->created_at)->format('d-m-Y H:i:s');
            }



            return response()->json(['response' => ['status' => true, 'data' => $documentos_unicos, 'message' => 'user']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

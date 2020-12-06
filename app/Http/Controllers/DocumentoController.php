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
                'archivo_descripcion' => $subirArchivo['descripcion_documento'],
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
            return response()->json(['response' => ['status' => true, 'data' => $archivo, 'message' => 'Centro de Costos Actualizado']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // SI ALGO SALE MAL HACEMOS ROLLBACK AL INICIO
            DB::rollback();
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function mis_documentos()
    {
        try {
            $documentos = Documento::all();

            return response()->json(['response' => ['status' => true, 'data' => $documentos, 'message' => 'Centro de Costos Actualizado']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

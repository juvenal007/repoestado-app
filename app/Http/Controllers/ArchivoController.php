<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Archivo;
use Illuminate\Http\Request;


class ArchivoController extends Controller
{
    public function list()
    {
        try {
            $archivo = Archivo::all();
            return response()->json(['response' => ['status' => true, 'data' => $archivo, 'message' => 'Lista de archivos']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
    public function verArchivo($id)
    {
        try{
            $documento = Documento::find($id);
            $archivo = Archivo::where('documento_id', $documento->id)->get()->first();
            return response()->json(['response' => ['status' => true, 'data' => $archivo, 'message' => 'Lista de archivos']], 200);
        }catch(Exception $e)
        {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
    public function verFolio($id)
    {
        try{
            $documento = Documento::find($id);
            $archivo = Archivo::where('documento_id', $documento['id'])->where('archivo_descripcion', "FOLIO")->get()->first();
            return response()->json(['response' => ['status' => true, 'data' => $archivo, 'message' => 'Lista de archivos']], 200);
        }catch(Exception $e)
        {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

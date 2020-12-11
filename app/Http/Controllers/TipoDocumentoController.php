<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoDocumentoController extends Controller
{
    public function list()
    {
        $tipo_documento = DB::table('tipo_documentos')->get();
        return response()->json([
            'response' => [
                'status' => true,
                'data' => $tipo_documento,
                'message' => 'Query success'
            ]
        ], 200);
    }
    public function list_all()
    {
        try {
            $tipo_documento = TipoDocumento::all();
            return response()->json(['response' => ['status' => true, 'data' => $tipo_documento, 'message' => 'Lista Tipo Documentos']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

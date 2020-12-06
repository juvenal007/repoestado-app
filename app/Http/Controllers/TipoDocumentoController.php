<?php

namespace App\Http\Controllers;

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
}

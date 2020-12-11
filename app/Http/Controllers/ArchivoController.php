<?php

namespace App\Http\Controllers;

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
}

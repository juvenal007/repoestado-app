<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function list($departamento_id)
    {
        $usuarios = Usuario::with('departamento')->where('departamento_id', $departamento_id)->get();
        return response()->json([
            'response' => [
                'status' => true,
                'data' => $usuarios,
                'message' => 'Query success'
            ]
        ], 200);
    }
    public function list_all()
    {
        try {
            $usuario = Usuario::with('departamento')->get();
            return response()->json(['response' => ['status' => true, 'data' => $usuario, 'message' => 'Lista usuarios']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

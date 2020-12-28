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
            $usuarios = Usuario::with('departamento')->get();
            foreach($usuarios as $clave => $valor)
            {
                $usuarios[$clave]->departamento_txt = $usuarios[$clave]['departamento']->depto_nombre;
            }
            return response()->json(['response' => ['status' => true, 'data' => $usuarios, 'message' => 'Lista usuarios']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }

    public function add(Request $request)
    {
        try {
            $usuario = new Usuario($request->data);
            $usuario->password = bcrypt($request->data['usuario']);
            $usuario->usuario_rut = (int) $usuario->usuario_rut;
            $usuario->usuario_dv = substr($usuario->usuario_rut, -1);
            $usuario->save();
            return response()->json(['response' => ['status' => true, 'data' => $usuario, 'message' => 'Usuario creado.']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 500);
        }
    }
}

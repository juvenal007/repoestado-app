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
}

<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    public function list()
    {
        $departamentos = Departamento::all();
        return response()->json([
            'response' => [
                'status' => true,
                'data' => $departamentos,
                'message' => 'Query success'
            ]
        ], 200);
    }

    public function list_all()
    {
        try {
            $departamento = Departamento::all();
            return response()->json(['response' => ['status' => true, 'data' => $departamento, 'message' => 'Lista Departamentos']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 200);
        }
    }
    public function add(Request $request)
    {
        try{
            $departamento = new Departamento($request->data);
            $departamento->save();
            return response()->json(['response' => ['status' => true, 'data' => $departamento, 'message' => 'Departamento creado']], 201);
        }catch(\Illuminate\Database\QueryException $e)
        {
            return response()->json(['response' => ['type_error' => 'query_exception','status' => false,'data' => $e,'message' => 'Error processing']], 200);
        }

    }
}

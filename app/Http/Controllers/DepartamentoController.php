<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    public function list()
    {
        $departamentos = DB::table('departamentos')->get();
        return response()->json([
            'response' => [
                'status' => true,
                'data' => $departamentos,
                'message' => 'Query success'
            ]
        ], 200);
    }
}

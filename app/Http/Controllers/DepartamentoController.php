<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartamentoController extends Controller
{

    protected $messages = [
        'same' => 'El :attribute y :other deben coincidir.',
        'size' => 'El :attribute debe ser exactamente :size.',
        'between' => 'El valor de :attribute (:input) no esta entre :min - :max.',
        'in' => 'El :attribute debe ser uno de los siguientes tipos: :values',
        'required' => 'El atributo :attribute es requerido.',
        'max' => 'El :attribute  no puede exceder los :max caracteres',
        'confirmed' => 'La confirmacion del password no coincide.',
        'numeric' => 'El Valor de :attribute  debe ser un número',
        'unique' => 'El valor de :attribute ya existe',
    ];

    protected $customAttributes = [
        'depto_nombre' => 'Nombre',
        'depto_descripcion' => 'Descripción',
        'depto_telefono' => 'Telefono',
        'depto_anexo' => 'Anexo',
    ];

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
    public function edit(Request $request, $id)
    {
        try{
            $validar = Validator::make($request->data, [
                'depto_nombre' => 'required|max:100',
                'depto_descripcion' => 'required|max:200',
                'depto_telefono' => 'required|max:15',
                'depto_anexo' => 'required|max:15'
            ],$this->messages, $this->customAttributes);
            if($validar->fails()){
                return response()->json(['response' => ['type_error' => 'validation_error','status' => false,'data' =>$validar->errors(),'message' => 'Validation errors']], 200);
            }

            $departamento = Departamento::find($id);
            $departamento->depto_nombre = $request->data["depto_nombre"];
            $departamento->depto_descripcion = $request->data["depto_descripcion"];
            $departamento->depto_telefono = $request->data["depto_telefono"];
            $departamento->depto_anexo = $request->data["depto_anexo"];
            $departamento->save();
            return response()->json(['response' => ['status' => true,'data' => $departamento,'message' => 'Departamento actualizado']], 200);
        }catch(\Illuminate\Database\QueryException $e){
            return response()->json(['response' => ['type_error' => 'query_exception','status' => false,'data' => $e,'message' => 'Error processing']], 200);
        }
    }

    public function delete($id)
    {
        try {
            $usuarios = Usuario::where('departamento_id', $id)->get();
            $departamento = Departamento::find($id);

            if (!$usuarios) {
                return response()->json(['response' => ['type_error' => 'entity_not_found', 'status' => false, 'data' => [], 'message' => 'Departamento no existe']], 400);
            }

            if ($usuarios->count() > 0) {
                return response()->json(['response' => ['type_error' => 'not_allowed', 'status' => false, 'data' => [], 'message' => "No es posible eliminar el Departamento " . $departamento['depto_nombre'] . " ya que tiene usuarios"]], 200);
            }

            $departamento = Departamento::where('id',$id);
            $departamento->delete();

            return response()->json(['response' => ['status' => true, 'data' => $departamento, 'message' => 'Departamento Eliminado']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['response' => ['type_error' => 'query_exception', 'status' => false, 'data' => $e, 'message' => 'Error processing']], 200);
        }
    }
}

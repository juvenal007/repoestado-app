<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FileUploadController extends Controller
{
    public static function do_upload(Request $request)
    {
        try {
            $fileName = (string) $request['fileName'];          
            $tipo_documento = $request['tipo_documento'];
            $id_documento = $request['id_documento'];
            $nombre_documento = $request['nombre_documento'];
            $descripcion_documento = $request['descripcion_documento'];
            $destinatarios = $request['destinatarios'];                
            $user = $request['user'];   
            
            if ($request->file('file')->isValid()) {
                $extension = $request->file('file')->extension();
                $nombre = str_replace(' ', '_', $fileName);
                $fecha = date("Y-m-d-H-i-s");

                $final_name = "Documento-{$fecha}-{$nombre}";      
                $path = $request->file('file')->storeAs('documents', $final_name);
            }      

            $datos = [
                'nombre_archivo' => $final_name,
                'tipo_documento' => $tipo_documento,
                'id_documento' => $id_documento,
                'nombre_documento' => $nombre_documento,
                'descripcion_documento' => $descripcion_documento,
                'destinatarios' => $destinatarios,
                'extension' => $extension,
                'file' => $path,
                'user' => $user,
            ];                    
            return $datos;
        } catch (Exception $th) {
            return $th;
        }
      
    }
}

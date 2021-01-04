<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Estado;
use Illuminate\Http\Request;

class DashController extends Controller
{

    public function grafico_barra()
    {
        try {

            $departamentos = Departamento::all();

            $estados = Estado::with('usuario', 'usuario.departamento', 'documento', 'documento.tipo_documento')
                ->orderBy('id', 'DESC')
                ->get();

            $estados_unicos = [];

            foreach ($estados as $clave => $valor) {
                if (!$valor['documento'] == null && $valor['documento']['docu_estado'] == 'ENVIADO') {
                    if ($valor['estado_fecha_egreso'] == null) {
                        array_push($estados_unicos, $valor);
                    }
                }
            }

            $departamentos_estados = [];

            foreach($departamentos as $claveD => $valor)
            {
                $departamento = [];
                array_push($departamento, substr($valor->depto_nombre, 0 ,05));
                $aux= 0;
                foreach($estados_unicos as $claveE => $valor)
                {
                    if($estados_unicos[$claveE]['usuario']['departamento']['id'] == $departamentos[$claveD]->id)
                    {
                        $aux++;
                    }
                }
                array_push($departamento, $aux);
                array_push($departamentos_estados, $departamento);
            }
            return response()->json(['response' => ['status' => true, 'data' => $departamentos_estados, 'message' => 'Documentos Obtenidos con Exito']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
        }
    }
    public function grafico_circular()
    {
        try {

            $departamentos = Departamento::all();

            $estados = Estado::with('usuario', 'usuario.departamento', 'documento', 'documento.tipo_documento')
                ->orderBy('id', 'DESC')
                ->get();

            $estados_unicos = [];

            foreach ($estados as $clave => $valor) {
                if (!$valor['documento'] == null && $valor['documento']['docu_estado'] == 'ENVIADO') {
                    if ($valor['estado_fecha_egreso'] == null) {
                        array_push($estados_unicos, $valor);
                    }
                }
            }

            $departamentos_estados = [];

            foreach($departamentos as $claveD => $valor)
            {
                $departamento = (object) null;
                $departamento->label = substr($valor->depto_nombre, 0 ,5);
                $departamento->color = "#".$this->random_color();
                $aux= 0;
                foreach($estados_unicos as $claveE => $valor)
                {
                    if($estados_unicos[$claveE]['usuario']['departamento']['id'] == $departamentos[$claveD]->id)
                    {
                        $aux++;
                    }
                }
                $departamento->data = $aux;
                array_push($departamentos_estados, $departamento);
            }
            return response()->json(['response' => ['status' => true, 'data' => $departamentos_estados, 'message' => 'Documentos Obtenidos con Exito']], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
        }
    }

    public function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    public function random_color() {
        return $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }
}

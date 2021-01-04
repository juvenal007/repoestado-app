<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;


use Carbon\Carbon;
use App\Models\Archivo;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentsExport implements
    WithColumnWidths,
    WithHeadings,
    FromCollection,
    WithProperties
{
    /**
     * @return \Illuminate\Support\Collection
     */



    public function properties(): array
    {
        return [
            'creator'        => 'RepoEstado',
            'title'          => 'Reporte Documentos',
        ];
    }
    public function headings(): array
    {
        return [
            'Codigo',
            'Nombre Documento',
            'Tipo Documento',
            'Fecha Ingreso Documento',
            'Fecha Ingreso Estado',
            'Tiempo Mes',
            'Tiempo Día',
            'Tiempo Hora',
            'Tiempo Minuto',
            'Usuario Nombre',
            'Usuario Apellido',
            'Usuario Departamento',
            'Usuario Anexo',
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 20,
            'D' => 24,
            'E' => 24,
            'F' => 12,
            'G' => 12,
            'H' => 12,
            'I' => 14,
            'J' => 20,
            'K' => 20,
            'L' => 21,
            'M' => 15,
        ];
    }


    public function list_usuario()
    {
        try {

            $fecha = Carbon::parse(Carbon::now()->format('d-m-Y H:i:s'));
            $estados = Estado::with('usuario', 'usuario.departamento', 'documento', 'documento.tipo_documento')
                ->orderBy('id', 'DESC')
                ->get();
            /*   $estados = Estado::with('usuario', 'documento')->where('usuario_id', $id_usuario)->distinct('documento_id')->orderBy('id', 'DESC')->get(); */
            /*   return response()->json(['response' => ['status' => true, 'data' => $estados, 'message' => $estados]], 200); */
            $documentos_unicos = [];
            $estados_unicos = [];

            foreach ($estados as $clave => $valor) {
                if (!$valor['documento'] == null && $valor['documento']['docu_estado'] == 'ENVIADO') {
                    $documentos = Documento::where('id', $valor['documento']['id'])->first();
                    array_push($documentos_unicos, $documentos);
                    if ($valor['estado_fecha_egreso'] == null) {
                        array_push($estados_unicos, $valor);
                    }
                }
            }

            foreach ($estados_unicos as $clave => $valor) {

                $fechaNueva = Carbon::parse($valor['estado_fecha_ingreso'])->format('d-m-Y H:i:s');

                if (!$valor['estado_fecha_egreso'] == null) {

                    $fechaIngreso = Carbon::parse($valor['estado_fecha_ingreso'])->format('d-m-Y H:i:s');
                    $fechaIngreso = Carbon::create($fechaIngreso);

                    $fechaEgreso = Carbon::parse($valor['estado_fecha_egreso'])->format('d-m-Y H:i:s');
                    $fechaEgreso = Carbon::create($fechaEgreso);

                    $diferenciaMeses = $fechaEgreso->diffInMonths($fechaIngreso, true);
                    $diferenciaDias = $fechaEgreso->diffInDays($fechaIngreso->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fechaEgreso->diffInHours($fechaIngreso->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fechaEgreso->diffInMinutes($fechaIngreso->addHours($diferenciaHoras), true);

                    $estados_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $estados_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $estados_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $estados_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;

                    $estados_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor['documento']->created_at)->format('d-m-Y H:i:s');
                    $estados_unicos[$clave]->estado_fecha_ingreso = Carbon::parse($valor['estado_fecha_ingreso'])->format('d-m-Y H:i:s');
                } else {
                    $fechaCarbon = Carbon::create($fechaNueva);

                    // CALCULO LA DIFERENCIA DE DIAS
                    $diferenciaMeses = $fecha->diffInMonths($fechaCarbon, true);
                    $diferenciaDias = $fecha->diffInDays($fechaCarbon->addMonth($diferenciaMeses), true);
                    $diferenciaHoras = $fecha->diffInHours($fechaCarbon->addDays($diferenciaDias), true);
                    $diferenciaMinutos = $fecha->diffInMinutes($fechaCarbon->addHours($diferenciaHoras), true);
                    $estados_unicos[$clave]->diferenciaMeses = $diferenciaMeses;
                    $estados_unicos[$clave]->diferenciaDias = $diferenciaDias;
                    $estados_unicos[$clave]->diferenciaHoras = $diferenciaHoras;
                    $estados_unicos[$clave]->diferenciaMinutos = $diferenciaMinutos;
                    $estados_unicos[$clave]->docu_fecha_ingreso = Carbon::parse($valor['documento']->created_at)->format('d-m-Y H:i:s');
                    $estados_unicos[$clave]->estado_fecha_ingreso = Carbon::parse($valor['estado_fecha_ingreso'])->format('d-m-Y H:i:s');
                    $estados_unicos[$clave]->nombre_usuario = $valor['usuario']->usuario_nombre . " " . $valor['usuario']->usuario_ape_paterno;
                }
            }

            $excel = [];

            foreach ($estados_unicos as $clave => $valor) {
                //INFORMACIÓN DOCUMENTO
                $excel[$clave]['docu_codigo'] = $valor['documento']['docu_codigo'];
                $excel[$clave]['docu_nombre'] = substr($valor['documento']['docu_nombre'],0,10);
                $excel[$clave]['docu_tipo'] = $valor['documento']['tipo_documento']['tipo_documento_nombre'];
                $excel[$clave]['docu_fecha_ingreso'] = $valor['docu_fecha_ingreso'];
                $excel[$clave]['estado_fecha_ingreso'] = $valor['estado_fecha_ingreso'];
                $excel[$clave]['diferenciaMeses'] = $valor['diferenciaMeses'];
                $excel[$clave]['diferenciaDias'] = $valor['diferenciaDias'];
                $excel[$clave]['diferenciaHoras'] = $valor['diferenciaHoras'];
                $excel[$clave]['diferenciaMinutos'] = $valor['diferenciaMinutos'];
                //USUARIO
                $excel[$clave]['usuario_nombre'] = $valor['usuario']['usuario_nombre'];
                $excel[$clave]['usuario_ape_paterno'] = $valor['usuario']['usuario_ape_paterno'];
                $excel[$clave]['usuario_departamento'] = $valor['usuario']['departamento']['depto_nombre'];
                $excel[$clave]['usuario_anexo'] = $valor['usuario']['usuario_anexo'];
            }


            return $excel;
            
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
        }
    }

    public function collection()
    {
        $lista = $this->list_usuario();
        return collect($lista);
    }
}

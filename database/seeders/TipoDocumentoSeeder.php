<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_documentos')->insert(
            [
                [
                    'tipo_documento_nombre' => 'MEMORÁNDUM',
                    'tipo_documento_descripcion' => 'ARCHIVO 1 A MUCHOS'
                ],
                [
                    'tipo_documento_nombre' => 'CIRCULAR',
                    'tipo_documento_descripcion' => 'ARCHIVO 1 A MUCHOS'
                ],
                [
                    'tipo_documento_nombre' => 'OFICIO',
                    'tipo_documento_descripcion' => 'ARCHIVO ESCALONADO'
                ],
                [
                    'tipo_documento_nombre' => 'DECRETO',
                    'tipo_documento_descripcion' => 'ARCHIVO ESCALONADO'
                ],
                [
                    'tipo_documento_nombre' => 'RESOLUCIÓN',
                    'tipo_documento_descripcion' => 'ARCHIVO ESCALONADO'
                ],
                [
                    'tipo_documento_nombre' => 'OTRO',
                    'tipo_documento_descripcion' => 'ARCHIVO 1 A MUCHOS'
                ]
            ]
        );
    }
}

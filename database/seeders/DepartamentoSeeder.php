<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos')->insert([
            [
                'depto_nombre' => 'INFORMATICA',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ],
            [
                'depto_nombre' => 'TESORERIA',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ],
            [
                'depto_nombre' => 'CONTABILIDAD',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ],
            [
                'depto_nombre' => 'ADMINISTRACION',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ],
            [
                'depto_nombre' => 'ADQUISICIONES',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ],
            [
                'depto_nombre' => 'ASEO Y ORNATO',
                'depto_descripcion' => 'Departamento de Informatica',
                'depto_telefono' => '+56 9 1111 2222',
                'depto_anexo' => '429456'
            ]
        ]           
    );
    }
}

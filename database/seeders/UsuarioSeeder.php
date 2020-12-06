<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {            
        DB::table('usuarios')->insert([
            'usuario' => 'admin',
            'password' => bcrypt('admin'),
            'usuario_rut' => 17826025,
            'usuario_dv' => 1,
            'usuario_nombre' => 'Juvenal',
            'usuario_ape_paterno' => 'Salas',
            'usuario_ape_materno' => 'Sepulveda',
            'usuario_correo' => 'Juvenal519@gmail.com',
            'usuario_tipo' => 'ADMINISTRADOR',
            'usuario_telefono' => '+56 9 4911 0361',
            'usuario_funcion' => 'Programador Informatica',
            'usuario_anexo' => '2 67 4466',
            'departamento_id' => 1,
        ]);
        Usuario::factory()
        ->times(50)            
        ->create();
    }
}

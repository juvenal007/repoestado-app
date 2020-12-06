<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Usuario::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'usuario' => $this->faker->text(20),
            'password' => bcrypt('admin'),
            'usuario_rut' => (int) $this->faker->numerify($string = '######'),
            'usuario_dv' => $this->faker->numberBetween($min = 1, $max = 9),
            'usuario_nombre' => $this->faker->name(),
            'usuario_ape_paterno' => $this->faker->text(10),
            'usuario_ape_materno' => $this->faker->text(10),
            'usuario_correo' => $this->faker->email(),
            'usuario_tipo' => 'ADMINISTRADOR',
            'usuario_telefono' => $this->faker->e164PhoneNumber,
            'usuario_funcion' => $this->faker->text(20),
            'usuario_anexo' => $this->faker->numerify($string = '######'),
            'departamento_id' => $this->faker->numberBetween($min = 1, $max = 6),


        ];
        /* 'telefono' => $this->faker->e164PhoneNumber,
            'direccion' => $this->faker->address,   
            'proveedors_id' => $this->faker->numberBetween($min = 1, $max = 30) ,
            'solicitud_codigo' => $this->faker->text(5)."-".$this->faker->numerify($string = '###'),   */
    }
}

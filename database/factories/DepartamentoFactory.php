<?php

namespace Database\Factories;

use App\Models\Departamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartamentoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Departamento::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'depto_nombre' => $this->faker->text(20),
            'depto_descripcion' => $this->faker->text(20),
            'depto_telefono' => $this->faker->e164PhoneNumber,
            'depto_anexo' => $this->faker->numerify($string = '######')
        ];
        /* 'telefono' => $this->faker->e164PhoneNumber,
            'direccion' => $this->faker->address,   
            'proveedors_id' => $this->faker->numberBetween($min = 1, $max = 30) ,
            'solicitud_codigo' => $this->faker->text(5)."-".$this->faker->numerify($string = '###'),   */
    }
}

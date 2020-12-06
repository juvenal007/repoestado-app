<?php

namespace Database\Factories;

use App\Models\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoDocumentoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TipoDocumento::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tipo_documento_nombre' => $this->faker->text(10),
            'tipo_documento_descripcion' => $this->faker->text(50),            

        ];
        /* 'telefono' => $this->faker->e164PhoneNumber,
            'direccion' => $this->faker->address,   
            'proveedors_id' => $this->faker->numberBetween($min = 1, $max = 30) ,
            'solicitud_codigo' => $this->faker->text(5)."-".$this->faker->numerify($string = '###'),   */
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Marca;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marca>
 */
class MarcaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //$marcas = ['Don Vitorio', 'El Olivar', 'Tottus', 'Molitalia', 'Blanca flor', 'Ajinomoto', 'Marina', 'Costeña', 'Carbonel'];

        return [
            //
            //'nombre' => $this->faker->randomElement($marcas),
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(3),
            'activo' => $this->faker->boolean(90), // 90% probabilidad de estar activo
        ];
    }
}

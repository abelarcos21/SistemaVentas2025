<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proveedor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proveedor>
 */
class ProveedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            //
            'nombre' => $this->faker->company(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'codigo_postal' => $this->faker->postcode(),
            'sitio_web' => $this->faker->url(),
            'notas' => $this->faker->sentence(5),
        ];
    }
}

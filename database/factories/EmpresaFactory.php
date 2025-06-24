<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Empresa;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
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
            'razon_social' => $this->faker->company,
            'rfc' => strtoupper($this->faker->unique()->bothify('???######???')),
            'telefono' => $this->faker->phoneNumber,
            'correo' => $this->faker->unique()->safeEmail,
            'moneda' => $this->faker->randomElement(['MXN', 'USD', 'EUR']),
            'imagen' => null,
            'direccion' => $this->faker->address,
            'regimen_fiscal' => '601',
            'codigo_postal' => $this->faker->postcode,
        ];
    }
}

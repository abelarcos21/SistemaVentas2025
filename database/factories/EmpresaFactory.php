<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Empresa;
use App\Models\Moneda;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        // Obtener IDs de monedas disponibles
        $monedaIds = Moneda::pluck('id')->toArray();

        return [
            //
            'razon_social' => $this->faker->company,
            'rfc' => strtoupper($this->faker->unique()->bothify('???######???')),
            'telefono' => $this->faker->phoneNumber,
            'correo' => $this->faker->unique()->safeEmail,
            'moneda_id' => $this->faker->randomElement($monedaIds),
            'imagen' => null,
            'direccion' => $this->faker->address,
            'regimen_fiscal' => '601',
            'codigo_postal' => $this->faker->postcode,
        ];
    }

    // Método helper para crear empresa con moneda específica
    public function conMoneda(string $codigoMoneda){
        return $this->state(function (array $attributes) use ($codigoMoneda) {
            $moneda = Moneda::where('codigo', $codigoMoneda)->first();

            return [
                'moneda_id' => $moneda ? $moneda->id : null,
            ];
        });
    }

    // Método helper para empresa mexicana
    public function mexicana(){
        return $this->state(function (array $attributes) {
            $monedaMxn = Moneda::where('codigo', 'MXN')->first();

            return [
                'razon_social' => 'Mi Empresa S.A. de C.V.',
                'rfc' => 'ABC123456T12',
                'moneda_id' => $monedaMxn ? $monedaMxn->id : null,
                'regimen_fiscal' => '601',
                'codigo_postal' => '24000',
            ];
        });
    }

}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Marca;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marca>
 */
class MarcaFactory extends Factory
{

    protected $model = \App\Models\Marca::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = [
            'Coca-Cola',
            'Pepsi',
            'Bimbo',
            'Sabritas',
            'Lala',
            'Nestlé',
            'Colgate',
            'Palmolive',
            'Knorr',
            'Herdez',
            'Great Value',
            'Gamesa',
            'Axe',
            'Gillette',
            'Zote',
            'Nivea',
            'Sony',
            'Samsung',
            'HP',
            'Epson',
        ];

        return [
            'nombre' => $this->faker->unique()->randomElement($marcas),
            'activo' => $this->faker->boolean(90), // 90% probabilidad de estar activo
        ];
    }

}

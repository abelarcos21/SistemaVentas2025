<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Imagen;
use App\Models\Producto;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Imagen>
 */
class ImagenFactory extends Factory
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
            'producto_id' => Producto::factory(), // o asigna uno fijo si ya existen
            'nombre' => $this->faker->word() . '.jpg',
            'ruta' => 'storage/imagenes/' . $this->faker->uuid() . '.jpg',

        ];
    }
}

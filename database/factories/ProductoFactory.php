<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
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
            'user_id' => User::factory(),
            'categoria_id' => Categoria::factory(),
            'proveedor_id' => Proveedor::factory(),
            'codigo' => $this->faker->unique()->ean13(),
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(5),
            'cantidad' => $this->faker->numberBetween(0, 100),
            'precio_compra' => $this->faker->randomFloat(2, 10, 1000),
            'precio_venta' => $this->faker->randomFloat(2, 20, 2000),
            'activo' => $this->faker->boolean(90),
        ];
    }
}

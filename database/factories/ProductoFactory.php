<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Marca;

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

    protected static int $barcodeCounter = 1; //Contador estático para mantener el número secuencial entre llamadas

    public function definition(): array
    {
        // Generar código EAN-13 válido
        $base12 = '750' . str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);

        $suma = 0;
        for ($i = 0; $i < 12; $i++) {
            $digito = (int)$base12[$i];
            $suma += ($i % 2 === 0) ? $digito : $digito * 3;
        }

        $verificador = (10 - ($suma % 10)) % 10;
        $codigo = $base12 . $verificador;

        return [
            'user_id' => User::factory(),
            'categoria_id' => Categoria::factory(),
            'proveedor_id' => Proveedor::factory(),
            'marca_id' => $this->getRandomMarcaId(), // Usar marca existente
            'codigo' => $codigo,
            'barcode_path' => "barcodes/{$codigo}.png",
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(4),
            'cantidad' => $this->faker->numberBetween(0, 100),
            'precio_compra' => $this->faker->randomFloat(2, 10, 500),
            'precio_venta' => $this->faker->randomFloat(2, 20, 1000),
            'activo' => $this->faker->boolean(90),
        ];
    }

    /**
     * Obtener ID de marca existente aleatoria
     */
    private function getRandomMarcaId()
    {
        $marca = Marca::inRandomOrder()->first();

        // Si no hay marcas, crear una nueva
        if (!$marca) {
            return Marca::factory()->create()->id;
        }

        return $marca->id;
    }

    /**
     * Usar IDs específicos (para cuando los pasas desde el seeder)
     */
    public function withSpecificIds(array $ids): static
    {
        return $this->state(function (array $attributes) use ($ids) {
            return array_merge($attributes, $ids);
        });
    }
}

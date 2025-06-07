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

        // Generar número aleatorio de 8 dígitos (relleno con ceros a la izquierda)
        //$barcodeNumber = str_pad($this->faker->unique()->numberBetween(1, 99999999), 8, '0', STR_PAD_LEFT);



        // Generar número con 8 dígitos rellenado con ceros
        $barcodeNumber = str_pad(self::$barcodeCounter++, 8, '0', STR_PAD_LEFT);


        return [
            //
            'user_id' => User::factory(),
            'categoria_id' => Categoria::factory(),
            'proveedor_id' => Proveedor::factory(),
            'marca_id' => Marca::factory(),
            'codigo' => $this->faker->unique()->ean8(),
            'barcode_path' =>"barcodes/{$barcodeNumber}.png",
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(4),
            'cantidad' => $this->faker->numberBetween(0, 100),
            'precio_compra' => $this->faker->randomFloat(2, 10, 1000),
            'precio_venta' => $this->faker->randomFloat(2, 20, 2000),
            'activo' => $this->faker->boolean(90),
        ];
    }
}

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
        
        $precioVenta = $this->faker->randomFloat(2, 20, 1000);
        $permiteMyoreo = $this->faker->boolean(30); // 30% chance de permitir mayoreo
        $enOferta = $this->faker->boolean(20); // 20% chance de estar en oferta
        
        return [
            'user_id' => User::factory(),
            'categoria_id' => Categoria::factory(),
            'proveedor_id' => Proveedor::factory(),
            'marca_id' => $this->getRandomMarcaId(),
            'codigo' => $codigo,
            'barcode_path' => "barcodes/{$codigo}.png",
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(4),
            'cantidad' => $this->faker->numberBetween(0, 100),
            'precio_compra' => $this->faker->randomFloat(2, 10, 500),
            'precio_venta' => $precioVenta,
            'activo' => $this->faker->boolean(90),
            
            // Nuevos campos de mayoreo
            'permite_mayoreo' => $permiteMyoreo,
            'precio_mayoreo' => $permiteMyoreo ? 
                $this->faker->randomFloat(2, $precioVenta * 0.7, $precioVenta * 0.9) : // 10-30% descuento
                null,
            'cantidad_minima_mayoreo' => $permiteMyoreo ? 
                $this->faker->numberBetween(5, 50) : 
                10,
            
            // Nuevos campos de oferta
            'en_oferta' => $enOferta,
            'precio_oferta' => $enOferta ? 
                $this->faker->randomFloat(2, $precioVenta * 0.5, $precioVenta * 0.8) : // 20-50% descuento
                null,
            'fecha_inicio_oferta' => $enOferta ? 
                $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d') : 
                null,
            'fecha_fin_oferta' => $enOferta ? 
                $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d') : 
                null,
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
    
    /**
     * Estado para productos con mayoreo habilitado
     */
    public function withMayoreo(): static
    {
        return $this->state(function (array $attributes) {
            $precioVenta = $attributes['precio_venta'];
            return [
                'permite_mayoreo' => true,
                'precio_mayoreo' => $this->faker->randomFloat(2, $precioVenta * 0.7, $precioVenta * 0.9),
                'cantidad_minima_mayoreo' => $this->faker->numberBetween(5, 50),
            ];
        });
    }
    
    /**
     * Estado para productos en oferta
     */
    public function enOferta(): static
    {
        return $this->state(function (array $attributes) {
            $precioVenta = $attributes['precio_venta'];
            return [
                'en_oferta' => true,
                'precio_oferta' => $this->faker->randomFloat(2, $precioVenta * 0.5, $precioVenta * 0.8),
                'fecha_inicio_oferta' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'fecha_fin_oferta' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            ];
        });
    }
    
    /**
     * Estado para productos sin mayoreo ni oferta
     */
    public function basico(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'permite_mayoreo' => false,
                'precio_mayoreo' => null,
                'cantidad_minima_mayoreo' => 10,
                'en_oferta' => false,
                'precio_oferta' => null,
                'fecha_inicio_oferta' => null,
                'fecha_fin_oferta' => null,
            ];
        });
    }
}
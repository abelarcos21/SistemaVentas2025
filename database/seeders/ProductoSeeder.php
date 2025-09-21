<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Marca;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan marcas
        $marcasCount = Marca::count();
        if ($marcasCount === 0) {
            $this->command->error('No hay marcas disponibles. Ejecuta primero MarcaSeeder.');
            return;
        }

        $this->command->info("Encontradas {$marcasCount} marcas disponibles.");

        // Crear productos con diferentes configuraciones
        $this->createProductosBasicos();
        $this->createProductosConMayoreo();
        $this->createProductosEnOferta();
        $this->createProductosCompletos();
    }
    
    /**
     * Crear productos básicos (sin mayoreo ni oferta)
     */
    private function createProductosBasicos(): void
    {
        $this->command->info('Creando productos básicos...');
        
        Producto::factory()
            ->count(10)
            ->basico()
            ->withSpecificIds([
                'user_id' => 1,
                'moneda_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
            ])
            ->create()
            ->each(function ($producto) {
                $this->generateBarcode($producto);
                $this->command->info("✓ Producto básico: {$producto->nombre}");
            });
    }
    
    /**
     * Crear productos con mayoreo habilitado
     */
    private function createProductosConMayoreo(): void
    {
        $this->command->info('Creando productos con mayoreo...');
        
        Producto::factory()
            ->count(8)
            ->withMayoreo()
            ->withSpecificIds([
                'user_id' => 1,
                'moneda_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
            ])
            ->create()
            ->each(function ($producto) {
                $this->generateBarcode($producto);
                $this->command->info("✓ Producto mayoreo: {$producto->nombre} - Precio mayoreo: $" . number_format($producto->precio_mayoreo, 2) . " (min: {$producto->cantidad_minima_mayoreo})");
            });
    }
    
    /**
     * Crear productos en oferta
     */
    private function createProductosEnOferta(): void
    {
        $this->command->info('Creando productos en oferta...');
        
        Producto::factory()
            ->count(7)
            ->enOferta()
            ->withSpecificIds([
                'user_id' => 1,
                'moneda_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
            ])
            ->create()
            ->each(function ($producto) {
                $this->generateBarcode($producto);
                $this->command->info("✓ Producto oferta: {$producto->nombre} - Precio oferta: $" . number_format($producto->precio_oferta, 2) . " (hasta: {$producto->fecha_fin_oferta})");
            });
    }
    
    /**
     * Crear productos con mayoreo Y oferta
     */
    private function createProductosCompletos(): void
    {
        $this->command->info('Creando productos completos (mayoreo + oferta)...');
        
        Producto::factory()
            ->count(5)
            ->withMayoreo()
            ->enOferta()
            ->withSpecificIds([
                'user_id' => 1,
                'moneda_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
            ])
            ->create()
            ->each(function ($producto) {
                $this->generateBarcode($producto);
                $this->command->info("✓ Producto completo: {$producto->nombre} - Mayoreo: $" . number_format($producto->precio_mayoreo, 2) . " | Oferta: $" . number_format($producto->precio_oferta, 2));
            });
    }
    
    /**
     * Generar código de barras físico
     */
    private function generateBarcode(Producto $producto): void
    {
        $code = basename($producto->barcode_path, '.png');
        $barcodeImage = DNS1D::getBarcodePNG($code, 'EAN13');
        $path = public_path($producto->barcode_path);

        // Asegurarse que el directorio existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, base64_decode($barcodeImage));
    }
}
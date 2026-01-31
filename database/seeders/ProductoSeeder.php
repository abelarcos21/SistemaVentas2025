<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Unidad;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //VERIFICACIÓN Y CREACIÓN DE MARCAS
        $marcasCount = Marca::count();
        if ($marcasCount === 0) {
            $this->command->error('No hay marcas. Ejecuta MarcaSeeder o crea marcas primero.');
            return;
        }

        //VERIFICACIÓN Y AUTO-CREACIÓN DE UNIDADES
        if (Unidad::count() === 0) {
            $this->command->info('No hay unidades. Ejecutando UnidadSeeder automáticamente...');
            $this->call(UnidadSeeder::class);
        }

        // Obtener IDs de unidades comunes para usarlas específicamente
        $pza = Unidad::where('abreviatura', 'pza')->first()->id;
        $kg  = Unidad::where('abreviatura', 'kg')->first()->id;

        $this->command->info("Iniciando creación de productos...");

        // Crear productos con diferentes configuraciones y Pasamos las unidades a los métodos para usarlas
        $this->createProductosBasicos($pza, $kg);
        $this->createProductosConMayoreo($pza);
        $this->createProductosEnOferta($pza);
        $this->createProductosCompletos($pza);
    }

    /**
     * Crear productos básicos (sin mayoreo ni oferta) Aceptamos argumentos opcionales para forzar unidades
     */
    private function createProductosBasicos($idPieza, $idKilo): void
    {
        $this->command->info('Creando productos básicos...');

        // Creamos 5 Piezas
        Producto::factory()->count(5)->basico()
            ->withSpecificIds([
                'user_id' => 1, 'categoria_id' => 2, 'proveedor_id' => 3,
                'unidad_id' => $idPieza // <--- Forzamos Pieza
            ])
            ->create()
            ->each(function ($producto){
                $producto->load('unidad'); //Carga la relación
                $this->generateBarcode($producto);
            });

        // Creamos 5 Kilos (para probar decimales en ventas luego)
        Producto::factory()->count(5)->basico()
            ->withSpecificIds([
                'user_id' => 1, 'categoria_id' => 2, 'proveedor_id' => 3,
                'unidad_id' => $idKilo // <--- Forzamos Kilos
            ])
            ->create()
            ->each(function ($producto){
                $producto->load('unidad'); //Carga la relación
                $this->generateBarcode($producto);
            });

        $this->command->info("✓ 10 Productos básicos creados (Mix Piezas/Kilos)");
    }

    /**
     * Crear productos con mayoreo habilitado
     */
    private function createProductosConMayoreo($defaultUnidad): void
    {
        $this->command->info('Creando productos con mayoreo...');

        Producto::factory()
            ->count(8)
            ->withMayoreo()
            ->withSpecificIds([
                'user_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
                'unidad_id' => $defaultUnidad
            ])
            ->create()
            ->each(function ($producto) {
                $producto->load('unidad'); //Carga la relación
                $this->generateBarcode($producto);
                $this->command->info("✓ Mayoreo: {$producto->nombre} | {$producto->unidad->abreviatura}");
            });
    }

    /**
     * Crear productos en oferta
     */
    private function createProductosEnOferta($defaultUnidad): void
    {
        $this->command->info('Creando productos en oferta...');

        Producto::factory()
            ->count(7)
            ->enOferta()
            ->withSpecificIds([
                'user_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
                'moneda_id' => 1,
                'unidad_id' => $defaultUnidad

            ])
            ->create()
            ->each(function ($producto) {
                $producto->load('unidad'); //Carga la relación
                $this->generateBarcode($producto);
                $this->command->info("✓ Producto oferta: {$producto->nombre} - Precio oferta: $" . number_format($producto->precio_oferta, 2) . " (hasta: {$producto->fecha_fin_oferta})");
            });
    }

    /**
     * Crear productos con mayoreo Y oferta
     */
    private function createProductosCompletos($defaultUnidad): void
    {
        $this->command->info('Creando productos completos (mayoreo + oferta)...');

        Producto::factory()
            ->count(5)
            ->withMayoreo()
            ->enOferta()
            ->withSpecificIds([
                'user_id' => 1,
                'categoria_id' => 2,
                'proveedor_id' => 3,
                'moneda_id' => 1,
                'unidad_id' => $defaultUnidad

            ])
            ->create()
            ->each(function ($producto) {
                $producto->load('unidad'); //Carga la relación
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

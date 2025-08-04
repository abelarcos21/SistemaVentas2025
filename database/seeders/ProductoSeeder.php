<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Marca;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D; //uso para generar el barcode crear físicamente el código de barras PNG

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

        //crear físicamente el código de barras PNG en la ruta
        //crear 10 productos
        //asignar un usuario/proveedor/categoría ya existentes:
        Producto::factory()->count(30)->withSpecificIds([
            'user_id' => 1,
            'categoria_id' => 2,
            'proveedor_id' => 3,
        ])->create()->each(function ($producto) {
            $code = basename($producto->barcode_path, '.png');
            $barcodeImage = DNS1D::getBarcodePNG($code, 'EAN13');

            $path = public_path($producto->barcode_path);

            // Asegurarse que el directorio existe
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, base64_decode($barcodeImage));

            $this->command->info("Producto creado: {$producto->nombre} - Marca: {$producto->marca->nombre}");
        });
    }
}

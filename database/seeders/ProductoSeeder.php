<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D; //uso para generar el barcode crear físicamente el código de barras PNG

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //crear físicamente el código de barras PNG en la ruta


        //crear 5 productos
        //Producto::factory()->count(5)->create();

        //asignar un usuario/proveedor/categoría ya existentes:
        Producto::factory()->count(20)->create([
            'user_id' => 1,
            'categoria_id' => 2,
            'proveedor_id' => 3,
        ])->each(function ($producto){
            $code = basename($producto->barcode_path, '.png');
            $barcodeImage = DNS1D::getBarcodePNG($code, 'EAN13');

            $path = public_path($producto->barcode_path);

            // Asegurarse que el directorio existe
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, base64_decode($barcodeImage));
        });
    }
}

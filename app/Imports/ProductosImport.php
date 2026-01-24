<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation{

    //Decirle a Laravel que los encabezados están en la Fila 1
    public function headingRow(): int{

        return 1; // Le dice a Laravel:  los títulos están en la fila 1"
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    /**
    * Mapea cada fila del Excel a un modelo de Eloquent
    */
    public function model(array $row){

        // 1. OBTENER ID DEL USUARIO ACTUAL
        $userId = Auth::id(); // Asegúrate de tener: use Illuminate\Support\Facades\Auth; arriba

        // 2. CREAR CATEGORÍA (Agregando user_id)
        $categoria = Categoria::firstOrCreate(
            ['nombre' => $row['categoria']],
            [
                'descripcion' => 'Importada masivamente',
                'user_id' => $userId,
                'medida' => 'Pieza'
            ]
        );

        // 3. CREAR MARCA (Agregando user_id por si acaso también lo pide)
        $marca = Marca::firstOrCreate(
            ['nombre' => $row['marca']],
            [
                'user_id' => $userId
            ]
        );

        // 4. CREAR PROVEEDOR (Agregando user_id)
        $proveedor = Proveedor::firstOrCreate(
            ['nombre' => $row['proveedor']],
            [
                'telefono' => '0000000000',
                'email' => 'example@hotmail.com',
                'codigo_postal' => '24520',
                'direccion' => 'Sin dirección',
                'user_id' => $userId
            ]
        );

        // 5. FORMATEO DE CÓDIGO
        $codigo = str_pad($row['codigo'], 13, '0', STR_PAD_LEFT);

        // 6. RETORNAR PRODUCTO
        return new Producto([
            'user_id'       => $userId,
            'codigo'        => $codigo,
            'nombre'        => $row['nombre'],
            'descripcion'   => $row['descripcion'] ?? $row['nombre'],
            'categoria_id'  => $categoria->id,
            'marca_id'      => $marca->id,
            'proveedor_id'  => $proveedor->id,
            'moneda_id'     => 1,
            'precio_compra' => (float) $row['precio_compra'],
            'precio_venta'  => (float) $row['precio_venta'],
            'cantidad'      => (int) ($row['stock'] ?? 0),
            'activo'        => true,
            'moneda'        => 'MXN',
            'requiere_fecha_caducidad' => isset($row['fecha_caducidad']),
            'fecha_caducidad' => isset($row['fecha_caducidad'])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_caducidad'])
                : null,
            'permite_mayoreo' => isset($row['precio_mayoreo']),
            'precio_mayoreo'  => $row['precio_mayoreo'] ?? null,
            'cantidad_minima_mayoreo' => $row['minimo_mayoreo'] ?? 10,
        ]);
    }

    /**
     * Reglas de validación para evitar errores fatales
     */
    public function rules(): array{

        return [
            'codigo' => 'unique:productos,codigo', // Evita duplicar códigos
            'nombre' => 'required',
            'categoria' => 'required',
            // Quitamos validación estricta de precios por ahora para que no falle si el excel tiene formato texto
            /* 'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric', */
        ];
    }

}

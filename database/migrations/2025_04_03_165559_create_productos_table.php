<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            //LLAVES FORANEAS
            // Usuario que creó/modificó el producto
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')  // Previene eliminar usuario si tiene productos
                ->comment('Usuario que creó el producto');

            // Categoría del producto
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->onDelete('restrict')  //Previene eliminar categoría si tiene productos
                ->comment('Categoría del producto');

            // Proveedor principal
            $table->foreignId('proveedor_id')
                ->constrained('proveedores')
                ->onDelete('restrict')  //Previene eliminar proveedor si tiene productos
                ->comment('Proveedor principal del producto');

            // Marca del producto
            $table->foreignId('marca_id')
                ->constrained('marcas')
                ->onDelete('restrict')  //Previene eliminar marca si tiene productos
                ->comment('Marca del producto');

            // Unidad de medida
            $table->foreignId('unidad_id')
                ->constrained('unidades')
                ->onDelete('restrict')  // Previene eliminar unidad si tiene productos
                ->comment('Unidad de medida (pza, kg, lt, etc.)');

            // Moneda para precios
            $table->foreignId('moneda_id')
                ->default(1)
                ->constrained('monedas')
                ->onDelete('restrict')
                ->comment('Moneda para precios (default: MXN)');
                //asumo que en la tabla `monedas` hay un registro con id=1 para MXN

            //CAMPOS
            $table->string('codigo', 13)->unique()->nullable()->comment('Código de barras EAN-13 (único)');
            $table->string('barcode_path', 255)->nullable()->comment('Ruta del archivo PNG del código de barras');
            $table->string('nombre', 255)->comment('Nombre comercial del producto');;
            $table->text('descripcion')->nullable()->comment('Descripción detallada, ingredientes, características');
            $table->integer('cantidad')->default(0)->unsigned()->comment('Stock actual en inventario');
            $table->decimal('precio_compra', 10, 2)->default(0.00)->unsigned()->comment('Precio de compra/costo unitario');
            $table->decimal('precio_venta', 10, 2)->default(0.00)->unsigned()->comment('Precio de venta al público');
            $table->boolean('activo')->default(true) ->comment('Producto activo para venta');

            //CAMPOS RELACIONADOS A LOS PRODUCTOS CON OFERTAS Y MAYOREO
            $table->boolean('permite_mayoreo')->default(false)->comment('Permite venta por mayoreo');
            $table->decimal('precio_mayoreo', 10, 2)->nullable()->unsigned()->comment('Precio unitario para venta por mayoreo');
            $table->integer('cantidad_minima_mayoreo')->default(0)->unsigned()->comment('Cantidad mínima para precio de mayoreo');

            $table->boolean('en_oferta')->default(false)->comment('Producto actualmente en oferta');
            $table->decimal('precio_oferta', 10, 2)->nullable()->unsigned()->comment('Precio promocional de oferta');
            $table->date('fecha_inicio_oferta')->nullable()->comment('Fecha de inicio de la oferta');
            $table->date('fecha_fin_oferta')->nullable()->comment('Fecha de fin de la oferta');

            //CAMPOS PARA CONTROL FECHA DE CADUCIDAD

            // Si requiere control de fecha de vencimiento
            $table->boolean('requiere_fecha_caducidad')->default(false)->comment('Producto requiere control de vencimiento');
            // Fecha de caducidad/vencimiento
            $table->date('fecha_caducidad')->nullable()->comment('Fecha de vencimiento del producto');

            $table->timestamps();

            $table->softDeletes()->comment('Fecha de eliminación lógica');

            //-- Índices para rendimiento
            // Búsqueda por código de barras (muy común)
            $table->index('codigo', 'idx_productos_codigo');

            // Búsqueda por nombre (para autocompletado)
            $table->index('nombre', 'idx_productos_nombre');

            // Filtrar productos activos
            $table->index('activo', 'idx_productos_activo');

            // Filtrar productos en stock
            $table->index('cantidad', 'idx_productos_cantidad');

            // Búsqueda combinada para ofertas vigentes
            $table->index(
                ['en_oferta', 'fecha_inicio_oferta', 'fecha_fin_oferta'],
                'idx_productos_oferta'
            );

            // Filtrar productos con mayoreo
            $table->index('permite_mayoreo', 'idx_productos_mayoreo');

            // Búsqueda combinada para productos con caducidad próxima
            $table->index(
                ['requiere_fecha_caducidad', 'fecha_caducidad'],
                'idx_productos_caducidad'
            );

            //Índice compuesto para búsquedas comunes
            $table->index(['categoria_id', 'activo'], 'idx_productos_categoria_activo');
            $table->index(['marca_id', 'activo'], 'idx_productos_marca_activo');
            $table->index(['proveedor_id'], 'idx_productos_proveedor');

            //Índice para productos con stock bajo
            // (útil para alertas de inventario)
            $table->index(['cantidad', 'activo'], 'idx_productos_stock_bajo');

        });

        // COMENTARIO EN LA TABLA
        DB::statement("
            ALTER TABLE productos
            COMMENT = 'Catálogo maestro de productos con control de inventario, precios, ofertas y caducidad'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');

    }
};

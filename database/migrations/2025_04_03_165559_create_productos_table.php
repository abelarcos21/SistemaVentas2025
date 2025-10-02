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
            $table->foreignId('user_id')->constrained('users');//LLAVES FORANEA
            $table->foreignId('categoria_id')->constrained('categorias');//LLAVES FORANEA
            $table->foreignId('proveedor_id')->constrained('proveedores');//LLAVES FORANEA
            $table->foreignId('marca_id')->constrained('marcas');//LLAVES FORANEA

            // CLAVE FORÁNEA A MONEDAS
            $table->foreignId('moneda_id')->default(1)->constrained('monedas');
            //asumo que en la tabla `monedas` tienes un registro con id=1 para MXN

            //CAMPOS
            $table->string('codigo', 13)->unique()->nullable();
            $table->string('barcode_path')->nullable(); // Imagen del código de barras
            $table->string('nombre', 50);
            $table->string('descripcion', 500);//Descripción SAT o comercial
            $table->integer('cantidad')->default(0);
            $table->float('precio_compra')->default(0);
            $table->float('precio_venta')->default(0);
            $table->string('moneda', 3)->default('MXN');
            $table->boolean('activo')->default(true);

            //CAMPOS RELACIONADOS A LOS PRODUCTOS CON OFERTAS Y MAYOREO
            $table->boolean('permite_mayoreo')->default(false);
            $table->boolean('en_oferta')->default(false);
            $table->decimal('precio_mayoreo', 10, 2)->nullable();
            $table->decimal('precio_oferta', 10, 2)->nullable();
            $table->integer('cantidad_minima_mayoreo')->default(10);
            $table->date('fecha_inicio_oferta')->nullable();
            $table->date('fecha_fin_oferta')->nullable();

            //-- Índices para rendimiento
            $table->index(['en_oferta', 'fecha_inicio_oferta', 'fecha_fin_oferta'], 'idx_productos_oferta');
            $table->index('permite_mayoreo', 'idx_productos_mayoreo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');

    }
};

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
            $table->foreignId('user_id')->constrained('users');//LLAVES FORANEA
            $table->foreignId('categoria_id')->constrained('categorias');//LLAVES FORANEA
            $table->foreignId('proveedor_id')->constrained('proveedores');//LLAVES FORANEA
            $table->foreignId('marca_id')->constrained('marcas');//LLAVES FORANEA
            $table->string('codigo')->unique();
            $table->string('barcode_path')->nullable(); // Imagen del código de barras
            $table->string('nombre', 50);
            $table->string('descripcion', 500);//Descripción SAT o comercial
            $table->integer('cantidad')->default(0);
            $table->float('precio_compra')->default(0);
            $table->float('precio_venta')->default(0);
            $table->boolean('activo')->default(true);
            $table->string('clave_prod_serv', 10)->nullable(); // Clave SAT ClaveProdServ (ej: 01010101)
            $table->string('clave_unidad', 5)->nullable(); // Clave unidad SAT ClaveUnidad (ej: H87)
            $table->string('unidad_descripcion')->nullable(); //Unidad comercial Ej: "Pieza"
            $table->decimal('precio_unitario', 10, 2)->nullable();//para que pase el seeder se puso nullable
            $table->string('impuesto_trasladado')->nullable(); // 002 para IVA
            $table->decimal('tasa_o_cuota', 5, 4)->nullable(); // Ej. 0.1600
            $table->string('tipo_factor')->nullable(); // Tasa, Exento, Cuota
            $table->decimal('iva', 5, 2)->default(16.00); // Porcentaje de IVA
            $table->string('numero_identificacion')->nullable();
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

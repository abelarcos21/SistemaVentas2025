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
            $table->string('descripcion', 500);
            $table->integer('cantidad')->default(0);
            $table->float('precio_compra')->default(0);
            $table->float('precio_venta')->default(0);
            $table->boolean('activo')->default(true);
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

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
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();

            //Campos para registrar qué tipo de precio se aplicó en cada venta
            $table->enum('tipo_precio_aplicado', ['base', 'mayoreo', 'oferta'])->default('base');
            $table->decimal('precio_unitario_aplicado', 10, 2)->default(0);
            $table->decimal('descuento_aplicado', 10, 2)->default(0);

            $table->integer('cantidad');
            $table->float('precio_unitario', 10, 2);
            //$table->decimal('descuento', 10, 2)->default(0); // Descuento por línea
            $table->float('sub_total', 10, 2);// cantidad * precio_unitario - descuento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};

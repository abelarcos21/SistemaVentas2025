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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('Nombre completo de la unidad (ej: Kilogramo, Pieza, Litro, Servicio)');
            $table->string('abreviatura', 10)->comment('Abreviatura de la unidad (ej: kg, pza, lt, srv)');

            // Código oficial del SAT para facturación electrónica
            $table->string('codigo_sat', 10)->nullable()->comment('Código SAT para facturación electrónica (ej: KGM, H87, E48)');

            // Categoría para agrupar unidades similares
            $table->enum('tipo', ['peso', 'volumen', 'longitud', 'pieza', 'tiempo', 'otro'])->default('pieza')->comment('Tipo de medida para agrupar unidades');

            // Factor de conversión a unidad base (opcional)
            $table->decimal('factor_conversion', 10, 6)->nullable()->comment('Factor de conversión a unidad base (ej: 1kg = 1000g, factor=1000)');

            // Unidad base de referencia (opcional)
            $table->string('unidad_base', 50)->nullable()->comment('Unidad de referencia para conversión (ej: gramo para kilogramo)');

            // Si permite decimales
            $table->boolean('permite_decimales')->default(true)->comment('Si permite cantidades decimales (ej: 1.5 kg)');

            // Estado activo/inactivo
            $table->boolean('activo')->default(true)->comment('Unidad activa para uso en el sistema');

            // Descripción adicional
            $table->text('descripcion')->nullable()->comment('Descripción o notas adicionales sobre la unidad');
            $table->timestamps();
            $table->softDeletes();

            // ÍNDICES
            $table->index('activo', 'idx_unidades_activo');
            $table->index('tipo', 'idx_unidades_tipo');
            $table->index('codigo_sat', 'idx_unidades_codigo_sat');
            $table->index(['tipo', 'activo'], 'idx_unidades_tipo_activo');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};

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
        Schema::create('impuestos', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 3); // Ej: 002
            $table->enum('impuesto', ['ISR', 'IVA', 'IEPS']);
            $table->enum('tipo', ['Traslado', 'Retención']);
            $table->enum('factor', ['Tasa', 'Cuota', 'Exento']);
            $table->decimal('tasa', 5, 4)->nullable(); // Ej: 0.1600 para 16%
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impuestos');
    }
};

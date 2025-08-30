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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            // Nombre o razón social de la empresa (requerido)
            $table->string('razon_social');

            // RFC válido, único
            $table->string('rfc', 13)->unique();

            // Teléfono de contacto
            $table->string('telefono', 20)->nullable(); // puede ser opcional y con longitud acotada

            // Correo electrónico
            $table->string('correo')->unique();

            $table->unsignedBigInteger('moneda_id')->nullable();
            $table->foreign('moneda_id')->references('id')->on('monedas');

            // Logo o imagen de la empresa
            $table->string('imagen')->nullable();

            // Dirección completa
            $table->text('direccion')->nullable(); // puede no ser obligatoria al inicio

            // Nuevos campos útiles (opcional)
            $table->string('regimen_fiscal', 5)->nullable(); // Clave SAT si usarás CFDI 4.0
            $table->string('codigo_postal', 10)->nullable(); // Útil si usas catálogos SAT

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};

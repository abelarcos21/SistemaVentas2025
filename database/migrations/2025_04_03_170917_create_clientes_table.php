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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('calle');
            $table->string('numero_exterior');
            $table->string('numero_interior')->nullable();
            $table->string('codigo_postal');
            $table->string('estado');
            $table->string('municipio');
            $table->string('regimen_fiscal');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('rfc');
            $table->string('telefono');
            $table->string('correo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

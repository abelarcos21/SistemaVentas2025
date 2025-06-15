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
            $table->string('razon_social')->nullable();//para que pase el seeder se puso nullable
            $table->string('nombre_comercial')->nullable();
            $table->string('calle')->nullable();//para que pase el seeder se puso nullable
            $table->string('numero_exterior')->nullable();//para que pase el seeder se puso nullable
            $table->string('numero_interior')->nullable();
            $table->string('codigo_postal')->nullable();//para que pase el seeder se puso nullable;
            $table->string('estado')->nullable();//para que pase el seeder se puso nullable;
            $table->string('municipio')->nullable();//para que pase el seeder se puso nullable;
            $table->string('regimen_fiscal',3)->nullable();// clave SAT//para que pase el seeder se puso nullable;
            $table->string('uso_cfdi', 5)->nullable(); // clave SAT para que pase el seeder se puso nullable
            $table->string('nombre');
            $table->string('apellido');
            $table->string('rfc',13);
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

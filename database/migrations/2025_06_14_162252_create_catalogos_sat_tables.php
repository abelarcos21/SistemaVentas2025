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
        Schema::create('formas_pago', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique();
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique();
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('usos_cfdi', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique();// Ej: G03
            $table->string('descripcion');
            $table->boolean('persona_fisica');
            $table->boolean('persona_moral');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('regimenes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique(); // Ej: 601
            $table->string('descripcion');
            $table->boolean('persona_fisica'); // true = PF
            $table->boolean('persona_moral');  // true = PM
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('objetos_impuesto', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique();
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('claves_prod_serv', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 8)->unique(); // Ej: 01010101
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('claves_unidad', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 5)->unique(); // Ej. H87
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claves_unidad');
        Schema::dropIfExists('claves_prod_serv');
        Schema::dropIfExists('objetos_impuesto');
        Schema::dropIfExists('regimenes_fiscales');
        Schema::dropIfExists('usos_cfdi');
        Schema::dropIfExists('metodos_pago');
        Schema::dropIfExists('formas_pago');
    }
};

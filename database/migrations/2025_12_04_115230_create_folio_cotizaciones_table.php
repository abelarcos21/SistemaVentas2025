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
        Schema::create('folio_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 10)->unique();
            $table->integer('ultimo_numero')->default(0);
            $table->timestamps();
        });

        // Insertar serie inicial
        DB::table('folio_cotizaciones')->insert([
            'serie' => 'COT',
            'ultimo_numero' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folio_cotizaciones');
    }
};

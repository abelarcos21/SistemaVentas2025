<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonedaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('monedas')->insert([
            ['codigo' => 'MXN', 'nombre' => 'Peso Mexicano', 'simbolo' => '$'],
            ['codigo' => 'USD', 'nombre' => 'Dólar Estadounidense', 'simbolo' => '$'],
            ['codigo' => 'EUR', 'nombre' => 'Euro', 'simbolo' => '€'],
            ['codigo' => 'BRL', 'nombre' => 'Real Brasileño', 'simbolo' => 'R$'],
            ['codigo' => 'COP', 'nombre' => 'Peso Colombiano', 'simbolo' => '$'],
            ['codigo' => 'PEN', 'nombre' => 'Sol Peruano', 'simbolo' => 'S/.'],
            ['codigo' => 'CLP', 'nombre' => 'Peso Chileno', 'simbolo' => '$'],
            ['codigo' => 'ARS', 'nombre' => 'Peso Argentino', 'simbolo' => '$'],
            ['codigo' => 'UYU', 'nombre' => 'Peso Uruguayo', 'simbolo' => '$U'],
            ['codigo' => 'PYG', 'nombre' => 'Guaraní Paraguayo', 'simbolo' => '₲'],
            ['codigo' => 'BOB', 'nombre' => 'Boliviano', 'simbolo' => 'Bs.'],
            ['codigo' => 'VEF', 'nombre' => 'Bolívar Venezolano', 'simbolo' => 'Bs.'],
            ['codigo' => 'GTQ', 'nombre' => 'Quetzal Guatemalteco', 'simbolo' => 'Q'],
            ['codigo' => 'HNL', 'nombre' => 'Lempira Hondureño', 'simbolo' => 'L'],
            ['codigo' => 'NIO', 'nombre' => 'Córdoba Nicaragüense', 'simbolo' => 'C$'],
            ['codigo' => 'CRC', 'nombre' => 'Colón Costarricense', 'simbolo' => '₡'],
            ['codigo' => 'DOP', 'nombre' => 'Peso Dominicano', 'simbolo' => 'RD$'],
            ['codigo' => 'CUP', 'nombre' => 'Peso Cubano', 'simbolo' => '$'],
            ['codigo' => 'CAD', 'nombre' => 'Dólar Canadiense', 'simbolo' => 'C$'],
            ['codigo' => 'GBP', 'nombre' => 'Libra Esterlina', 'simbolo' => '£'],
        ]);
    }
}

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
        // Opción 1: Desactivar claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            DB::table('monedas')->truncate(); // Ahora funciona sin problemas
            
            DB::table('monedas')->insert([
                ['id' => 1, 'codigo' => 'MXN', 'nombre' => 'Peso Mexicano', 'simbolo' => '$'],
                ['id' => 2, 'codigo' => 'USD', 'nombre' => 'Dólar Estadounidense', 'simbolo' => '$'],
                ['id' => 3, 'codigo' => 'EUR', 'nombre' => 'Euro', 'simbolo' => '€'],
                ['id' => 4, 'codigo' => 'BRL', 'nombre' => 'Real Brasileño', 'simbolo' => 'R$'],
                ['id' => 5, 'codigo' => 'COP', 'nombre' => 'Peso Colombiano', 'simbolo' => '$'],
                ['id' => 6, 'codigo' => 'PEN', 'nombre' => 'Sol Peruano', 'simbolo' => 'S/.'],
                ['id' => 7, 'codigo' => 'CLP', 'nombre' => 'Peso Chileno', 'simbolo' => '$'],
                ['id' => 8, 'codigo' => 'ARS', 'nombre' => 'Peso Argentino', 'simbolo' => '$'],
                ['id' => 9, 'codigo' => 'UYU', 'nombre' => 'Peso Uruguayo', 'simbolo' => '$U'],
                ['id' => 10, 'codigo' => 'PYG', 'nombre' => 'Guaraní Paraguayo', 'simbolo' => '₲'],
                ['id' => 11, 'codigo' => 'BOB', 'nombre' => 'Boliviano', 'simbolo' => 'Bs.'],
                ['id' => 12, 'codigo' => 'VEF', 'nombre' => 'Bolívar Venezolano', 'simbolo' => 'Bs.'],
                ['id' => 13, 'codigo' => 'GTQ', 'nombre' => 'Quetzal Guatemalteco', 'simbolo' => 'Q'],
                ['id' => 14, 'codigo' => 'HNL', 'nombre' => 'Lempira Hondureño', 'simbolo' => 'L'],
                ['id' => 15, 'codigo' => 'NIO', 'nombre' => 'Córdoba Nicaragüense', 'simbolo' => 'C$'],
                ['id' => 16, 'codigo' => 'CRC', 'nombre' => 'Colón Costarricense', 'simbolo' => '₡'],
                ['id' => 17, 'codigo' => 'DOP', 'nombre' => 'Peso Dominicano', 'simbolo' => 'RD$'],
                ['id' => 18, 'codigo' => 'CUP', 'nombre' => 'Peso Cubano', 'simbolo' => '$'],
                ['id' => 19, 'codigo' => 'CAD', 'nombre' => 'Dólar Canadiense', 'simbolo' => 'C$'],
                ['id' => 20, 'codigo' => 'GBP', 'nombre' => 'Libra Esterlina', 'simbolo' => '£'],
            ]);
            
            $this->command->info('Monedas insertadas correctamente');
            
        } finally {
            // Reactivar claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
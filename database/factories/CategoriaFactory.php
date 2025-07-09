<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categoria;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Categoria::class;//Este factory es responsable de crear instancias del modelo App\Models\Categoria

    public function definition(): array
    {

       /*  $categorias = ['arroz', 'aceite', 'sal', 'condimentos', 'azúcar', 'harina', 'pastas', 'enlatados', 'lácteos']; */

        $categorias = [
            'Alimentos y Bebidas',
            'Abarrotes',
            'Panadería',
            'Lácteos',
            'Carnes y Embutidos',
            'Frutas y Verduras',
            'Dulcería',
            'Bebidas Alcohólicas',
            'Limpieza y Hogar',
            'Cuidado Personal',
            'Farmacia',
            'Electrónica',
            'Papelería',
            'Ropa y Accesorios',
            'Juguetería',
            'Mascotas',
            'Ferretería',
            'Automotriz',
            'Servicios',
        ];

        return [
            //
            'user_id' => User::factory(),
            'nombre' => $this->faker->randomElement($categorias),
            'descripcion' => $this->faker->sentence(2),
            'medida' => $this->faker->randomElement(['kg', 'litros', 'metros', 'unidades']),
            'activo' => $this->faker->boolean(90), // 90% probabilidad de estar activo

        ];
    }
}

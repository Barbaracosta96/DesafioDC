<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'               => $this->faker->words(3, true),
            'codigo_sku'         => strtoupper($this->faker->unique()->bothify('SKU-###-???')),
            'descricao'          => $this->faker->sentence(),
            'categoria_id'       => null,
            'preco_custo'        => $this->faker->randomFloat(2, 1, 100),
            'preco_venda'        => $this->faker->randomFloat(2, 10, 200),
            'quantidade_estoque' => $this->faker->numberBetween(10, 100),
            'estoque_minimo'     => 5,
            'unidade'            => 'un',
            'ativo'              => true,
        ];
    }
}

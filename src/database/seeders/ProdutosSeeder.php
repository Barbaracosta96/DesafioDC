<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutosSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Eletrônicos', 'descricao' => 'Produtos eletrônicos em geral'],
            ['nome' => 'Acessórios', 'descricao' => 'Acessórios para eletrônicos'],
            ['nome' => 'Fotografia', 'descricao' => 'Equipamentos fotográficos'],
            ['nome' => 'Vestuário', 'descricao' => 'Roupas e calçados'],
            ['nome' => 'Informática', 'descricao' => 'Computadores e periféricos'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['nome' => $cat['nome']], $cat);
        }

        $produtos = [
            ['categoria' => 'Fotografia',  'nome' => 'Lente de Câmera 50mm', 'sku' => 'FOTO-001', 'custo' => 450.00, 'venda' => 890.00, 'qtd' => 25],
            ['categoria' => 'Vestuário',   'nome' => 'NIKE Shoes Black Pattern', 'sku' => 'VEST-001', 'custo' => 150.00, 'venda' => 299.00, 'qtd' => 42],
            ['categoria' => 'Vestuário',   'nome' => 'Vestido Sleep Preto', 'sku' => 'VEST-002', 'custo' => 45.00, 'venda' => 89.00, 'qtd' => 30],
            ['categoria' => 'Eletrônicos', 'nome' => 'Fone Bluetooth Premium', 'sku' => 'ELET-001', 'custo' => 120.00, 'venda' => 249.99, 'qtd' => 18],
            ['categoria' => 'Informática', 'nome' => 'Mouse Sem Fio Ergonômico', 'sku' => 'INFO-001', 'custo' => 35.00, 'venda' => 79.90, 'qtd' => 60],
            ['categoria' => 'Informática', 'nome' => 'Teclado Mecânico RGB', 'sku' => 'INFO-002', 'custo' => 180.00, 'venda' => 349.00, 'qtd' => 15],
            ['categoria' => 'Acessórios',  'nome' => 'Cabo USB-C 2m', 'sku' => 'ACES-001', 'custo' => 8.00, 'venda' => 29.90, 'qtd' => 3],
            ['categoria' => 'Eletrônicos', 'nome' => 'Smartwatch Fitness', 'sku' => 'ELET-002', 'custo' => 220.00, 'venda' => 499.00, 'qtd' => 12],
            ['categoria' => 'Fotografia',  'nome' => 'Tripé Profissional 1.8m', 'sku' => 'FOTO-002', 'custo' => 85.00, 'venda' => 179.90, 'qtd' => 8],
            ['categoria' => 'Acessórios',  'nome' => 'Capa Protetora Celular', 'sku' => 'ACES-002', 'custo' => 12.00, 'venda' => 39.90, 'qtd' => 2],
        ];

        foreach ($produtos as $p) {
            $categoria = Categoria::where('nome', $p['categoria'])->first();

            Produto::firstOrCreate(['codigo_sku' => $p['sku']], [
                'categoria_id'       => $categoria?->id,
                'nome'               => $p['nome'],
                'codigo_sku'         => $p['sku'],
                'preco_custo'        => $p['custo'],
                'preco_venda'        => $p['venda'],
                'quantidade_estoque' => $p['qtd'],
                'estoque_minimo'     => 5,
                'unidade'            => 'un',
                'ativo'              => true,
            ]);
        }
    }
}

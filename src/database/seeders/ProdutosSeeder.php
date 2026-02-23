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
            ['nome' => 'Medicamentos',       'descricao' => 'Fármacos e medicamentos controlados e OTC'],
            ['nome' => 'Equipamentos Médicos', 'descricao' => 'Aparelhos e equipamentos para uso clínico'],
            ['nome' => 'Material de Curativo', 'descricao' => 'Curativos, ataduras e material cirúrgico'],
            ['nome' => 'Descartáveis',        'descricao' => 'Material descartável de uso único'],
            ['nome' => 'Higiene e Limpeza',   'descricao' => 'Produtos de higiene e antissépticos'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['nome' => $cat['nome']], $cat);
        }

        $produtos = [
            ['categoria' => 'Medicamentos',        'nome' => 'Dipirona Sódica 500mg — caixa 20 comp.', 'sku' => 'MED-001', 'custo' => 4.50,   'venda' => 12.90,  'qtd' => 200],
            ['categoria' => 'Medicamentos',        'nome' => 'Ibuprofeno 600mg — caixa 20 comp.',      'sku' => 'MED-002', 'custo' => 6.00,   'venda' => 18.50,  'qtd' => 150],
            ['categoria' => 'Medicamentos',        'nome' => 'Omeprazol 20mg — caixa 28 cáps.',        'sku' => 'MED-003', 'custo' => 8.00,   'venda' => 24.90,  'qtd' => 120],
            ['categoria' => 'Equipamentos Médicos', 'nome' => 'Esfigmomanômetro Digital de Pulso',       'sku' => 'EQP-001', 'custo' => 55.00,  'venda' => 129.90, 'qtd' => 30],
            ['categoria' => 'Equipamentos Médicos', 'nome' => 'Termômetro Clínico Digital',              'sku' => 'EQP-002', 'custo' => 12.00,  'venda' => 34.90,  'qtd' => 80],
            ['categoria' => 'Equipamentos Médicos', 'nome' => 'Oxímetro de Pulso Portátil',              'sku' => 'EQP-003', 'custo' => 38.00,  'venda' => 89.90,  'qtd' => 45],
            ['categoria' => 'Material de Curativo', 'nome' => 'Curativo Adesivo Estéril — caixa 100un.', 'sku' => 'CUR-001', 'custo' => 9.00,   'venda' => 22.00,  'qtd' => 300],
            ['categoria' => 'Material de Curativo', 'nome' => 'Gaze Estéril 7,5x7,5cm — pacote 10un.',  'sku' => 'CUR-002', 'custo' => 3.50,   'venda' => 9.90,   'qtd' => 500],
            ['categoria' => 'Descartáveis',        'nome' => 'Seringas 5ml com Agulha — caixa 100un.', 'sku' => 'DSC-001', 'custo' => 18.00,  'venda' => 45.00,  'qtd' => 40],
            ['categoria' => 'Descartáveis',        'nome' => 'Luvas Procedimento M — caixa 100un.',    'sku' => 'DSC-002', 'custo' => 22.00,  'venda' => 55.90,  'qtd' => 60],
            ['categoria' => 'Higiene e Limpeza',   'nome' => 'Álcool Etílico 70% — frasco 1L',         'sku' => 'HIG-001', 'custo' => 6.50,   'venda' => 15.90,  'qtd' => 180],
            ['categoria' => 'Higiene e Limpeza',   'nome' => 'Sabonete Antisséptico Hospitalar 500ml',  'sku' => 'HIG-002', 'custo' => 9.00,   'venda' => 24.50,  'qtd' => 3],
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

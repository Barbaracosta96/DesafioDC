<template>
  <AppLayout titulo="Produto">
    <Head :title="produto.nome">

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('estoque.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <div>
        <h2 class="text-2xl font-bold text-gray-900">{{ produto.nome }}</h2>
        <p class="text-xs text-gray-400 font-mono mt-0.5">SKU: {{ produto.codigo_sku || 'N/A' }}</p>
      </div>
      <div class="ml-auto flex gap-2">
        <Badge :variant="produto.ativo ? 'verde' : 'cinza'">{{ produto.ativo ? 'Ativo' : 'Inativo' }}</Badge>
        <Badge v-if="produto.estoque_baixo" variant="vermelho">Estoque Baixo</Badge>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Detalhes do produto -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Informações</h3>
          <dl class="space-y-3">
            <div>
              <dt class="text-xs text-gray-400">Categoria</dt>
              <dd class="text-sm text-gray-700">{{ produto.categoria?.nome ?? 'Sem categoria' }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-400">Unidade</dt>
              <dd class="text-sm text-gray-700">{{ produto.unidade }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-400">Preço de Custo</dt>
              <dd class="text-sm text-gray-700">R$ {{ formatarValor(produto.preco_custo) }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-400">Preço de Venda</dt>
              <dd class="text-sm font-semibold text-indigo-600">R$ {{ formatarValor(produto.preco_venda) }}</dd>
            </div>
            <div v-if="produto.descricao">
              <dt class="text-xs text-gray-400">Descrição</dt>
              <dd class="text-sm text-gray-600">{{ produto.descricao }}</dd>
            </div>
          </dl>
          <div class="flex gap-2 mt-5">
            <Botao :href="route('estoque.edit', produto.id)" class="flex-1 justify-center">Editar</Botao>
          </div>
        </div>

        <!-- Estoque -->
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold" :class="produto.estoque_baixo ? 'text-red-500' : 'text-gray-900'">{{ produto.quantidade_estoque }}</p>
            <p class="text-xs text-gray-500 mt-1">Em estoque</p>
          </div>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-amber-500">{{ produto.estoque_minimo }}</p>
            <p class="text-xs text-gray-500 mt-1">Estoque mínimo</p>
          </div>
        </div>
      </div>

      <!-- Histórico de movimentações -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Histórico de Movimentações</h3>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Tipo</th>
                <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Qtd</th>
                <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Anterior → Posterior</th>
                <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Motivo</th>
                <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Usuário</th>
                <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Data</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="mov in movimentacoes" :key="mov.id" class="hover:bg-gray-50">
                <td class="px-6 py-3">
                  <Badge :variant="corMovimentacao(mov.tipo)">{{ mov.tipo_label }}</Badge>
                </td>
                <td class="px-4 py-3 text-sm text-center font-medium" :class="mov.tipo === 'entrada' ? 'text-emerald-600' : mov.tipo === 'saida' ? 'text-red-500' : 'text-amber-600'">
                  {{ mov.tipo === 'saida' ? '-' : '+' }}{{ mov.quantidade }}
                </td>
                <td class="px-4 py-3 text-sm text-center text-gray-500">{{ mov.quantidade_anterior }} → {{ mov.quantidade_posterior }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ mov.motivo || '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ mov.usuario?.name ?? 'Sistema' }}</td>
                <td class="px-4 py-3 text-sm text-gray-400">{{ mov.data }}</td>
              </tr>
              <tr v-if="!movimentacoes.length">
                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma movimentação registrada.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
  produto:       { type: Object, required: true },
  movimentacoes: { type: Array,  default: () => [] },
});

const formatarValor     = (v) => Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
const corMovimentacao   = (t) => ({ entrada: 'verde', saida: 'vermelho', ajuste: 'amarelo' }[t] ?? 'cinza');
</script>

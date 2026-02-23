<template>
  <AppLayout titulo="Detalhes da Venda">
    <Head :title="`Venda ${venda.numero_pedido}`" />

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('vendas.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <div>
        <h2 class="text-2xl font-bold text-gray-900">{{ venda.numero_pedido }}</h2>
        <p class="text-sm text-gray-500">{{ venda.data_venda }}</p>
      </div>
      <Badge :variant="corStatus(venda.status)" class="ml-auto">{{ venda.status_label }}</Badge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Coluna esquerda: info principal -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Itens da venda -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Itens da Venda</h3>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Produto</th>
                <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Qtd</th>
                <th class="text-right text-xs font-medium text-gray-500 px-4 py-3 uppercase">Preço Unit.</th>
                <th class="text-right text-xs font-medium text-gray-500 px-4 py-3 uppercase">Desconto</th>
                <th class="text-right text-xs font-medium text-gray-500 px-6 py-3 uppercase">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="item in venda.itens" :key="item.id">
                <td class="px-6 py-3">
                  <p class="text-sm font-medium text-gray-900">{{ item.produto }}</p>
                  <p class="text-xs text-gray-400">{{ item.codigo_sku }}</p>
                </td>
                <td class="px-4 py-3 text-sm text-center text-gray-700">{{ item.quantidade }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ item.preco_unitario }}</td>
                <td class="px-4 py-3 text-sm text-right text-red-500">{{ item.desconto > 0 ? '-' + item.desconto_fmt : '—' }}</td>
                <td class="px-6 py-3 text-sm text-right font-medium text-gray-900">{{ item.subtotal }}</td>
              </tr>
            </tbody>
          </table>
          <!-- Totais -->
          <div class="px-6 py-4 border-t border-gray-100 space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
              <span>Subtotal</span>
              <span>{{ venda.subtotal }}</span>
            </div>
            <div v-if="venda.desconto_valor > 0" class="flex justify-between text-sm text-red-500">
              <span>Desconto</span>
              <span>-{{ venda.desconto_fmt }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100">
              <span>Total</span>
              <span>{{ venda.total }}</span>
            </div>
          </div>
        </div>

        <!-- Observações -->
        <div v-if="venda.observacoes" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-semibold text-gray-900 mb-3">Observações</h3>
          <p class="text-sm text-gray-600 whitespace-pre-line">{{ venda.observacoes }}</p>
        </div>
      </div>

      <!-- Coluna direita: meta info -->
      <div class="space-y-6">
        <!-- Cliente -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Cliente</h3>
          <div class="space-y-3">
            <div>
              <p class="text-xs text-gray-400">Nome</p>
              <p class="text-sm font-medium text-gray-900">{{ venda.cliente_nome }}</p>
            </div>
            <div v-if="venda.cliente_email">
              <p class="text-xs text-gray-400">E-mail</p>
              <p class="text-sm text-gray-700">{{ venda.cliente_email }}</p>
            </div>
            <div v-if="venda.cliente_telefone">
              <p class="text-xs text-gray-400">Telefone</p>
              <p class="text-sm text-gray-700">{{ venda.cliente_telefone }}</p>
            </div>
          </div>
        </div>

        <!-- Informações do Pedido -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Informações do Pedido</h3>
          <div class="space-y-3">
            <div>
              <p class="text-xs text-gray-400">Status</p>
              <Badge :variant="corStatus(venda.status)" class="mt-1">{{ venda.status_label }}</Badge>
            </div>
            <div>
              <p class="text-xs text-gray-400">Forma de Pagamento</p>
              <p class="text-sm text-gray-700">{{ venda.forma_pagamento_label }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">Vendedor</p>
              <p class="text-sm text-gray-700">{{ venda.vendedor }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">Data da Venda</p>
              <p class="text-sm text-gray-700">{{ venda.data_venda }}</p>
            </div>
          </div>
        </div>

        <!-- Ações -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
          <Botao :href="route('vendas.edit', venda.id)" class="w-full justify-center">Editar Venda</Botao>
          <Botao variant="perigo" class="w-full justify-center" v-if="venda.status !== 'cancelado'" @click="cancelarVenda">Cancelar Venda</Botao>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Botao from '@/Components/Botao.vue';

const props = defineProps({
  venda: { type: Object, required: true },
});

const corStatus = (s) => ({ concluido: 'verde', pendente: 'amarelo', processando: 'azul', cancelado: 'vermelho' }[s] ?? 'cinza');

const cancelarVenda = () => {
  if (confirm('Tem certeza que deseja cancelar esta venda?')) {
    router.patch(route('vendas.update', props.venda.id), { status: 'cancelado' });
  }
};
</script>

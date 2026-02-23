<template>
  <AppLayout titulo="Vendas">
    <Head title="Vendas" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Acompanhamento de Vendas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie e acompanhe todos os pedidos</p>
      </div>
      <Botao :href="route('vendas.create')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Venda
      </Botao>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vendas Hoje</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ resumo.total_hoje }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Receita Hoje</p>
        <p class="text-xl font-bold text-green-600 mt-1">{{ resumo.receita_hoje }}</p>
      </div>
      <div class="bg-white rounded-xl border border-yellow-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Pendentes</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ resumo.pendentes }}</p>
      </div>
      <div class="bg-white rounded-xl border border-red-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-red-500 uppercase tracking-wide">Canceladas (mês)</p>
        <p class="text-2xl font-bold text-red-500 mt-1">{{ resumo.canceladas }}</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <input
          v-model="filtroForm.busca"
          type="search"
          placeholder="Buscar por número ou cliente..."
          class="flex-1 min-w-48 px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
        <select v-model="filtroForm.status" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="">Todos status</option>
          <option value="pendente">Pendente</option>
          <option value="processando">Processando</option>
          <option value="concluido">Concluído</option>
          <option value="cancelado">Cancelado</option>
        </select>
        <input v-model="filtroForm.data_inicio" type="date" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input v-model="filtroForm.data_fim" type="date" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <Botao type="submit" size="md">Filtrar</Botao>
      </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Nº Pedido</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Cliente</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Vendedor</th>
            <th class="text-right text-xs font-medium text-gray-500 px-4 py-3 uppercase">Total</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Pagamento</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Status</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Data</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="venda in vendas.data" :key="venda.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm font-semibold text-indigo-700">{{ venda.numero_pedido }}</td>
            <td class="px-4 py-4 text-sm text-gray-700">{{ venda.cliente }}</td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ venda.vendedor }}</td>
            <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right">R$ {{ venda.total }}</td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ venda.forma_pagamento }}</td>
            <td class="px-4 py-4 text-center">
              <Badge :variant="badgeStatus(venda.status)">{{ venda.status_label }}</Badge>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ venda.data }}</td>
            <td class="px-4 py-4">
              <div class="flex items-center justify-center gap-2">
                <Link :href="route('vendas.show', venda.id)" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
                <Link :href="route('vendas.edit', venda.id)" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button
                  v-if="venda.status !== 'cancelado'"
                  class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  @click="confirmarCancelamento(venda)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!vendas.data.length">
            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
              Nenhuma venda encontrada.
            </td>
          </tr>
        </tbody>
      </table>

      <Paginacao
        :links="vendas.links"
        :de="vendas.from"
        :ate="vendas.to"
        :total="vendas.total"
        class="px-6 py-4 border-t border-gray-100"
      />
    </div>

    <!-- Modal Cancelamento -->
    <Modal :aberto="!!vendaCancelar" titulo="Cancelar Venda" @fechar="vendaCancelar = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja cancelar o pedido
        <strong class="text-gray-900">{{ vendaCancelar?.numero_pedido }}</strong>?
      </p>
      <template #footer>
        <Botao variant="secundario" @click="vendaCancelar = null">Voltar</Botao>
        <Botao variant="perigo" :carregando="cancelando" @click="cancelar">Cancelar Pedido</Botao>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import Paginacao from '@/Components/Paginacao.vue';

const props = defineProps({
  vendas:  { type: Object, required: true },
  filtros: { type: Object, default: () => ({}) },
  resumo:  { type: Object, default: () => ({}) },
});

const filtroForm  = ref({ busca: props.filtros.busca ?? '', status: props.filtros.status ?? '', data_inicio: props.filtros.data_inicio ?? '', data_fim: props.filtros.data_fim ?? '' });
const filtrar     = () => router.get(route('vendas.index'), filtroForm.value, { preserveState: true });

const vendaCancelar = ref(null);
const cancelando    = ref(false);
const confirmarCancelamento = (v) => { vendaCancelar.value = v; };
const cancelar = () => {
  cancelando.value = true;
  router.delete(route('vendas.destroy', vendaCancelar.value.id), {
    onFinish: () => { cancelando.value = false; vendaCancelar.value = null; },
  });
};

const badgeStatus = (s) => ({ pendente: 'amarelo', processando: 'azul', concluido: 'verde', cancelado: 'vermelho' }[s] ?? 'cinza');
</script>

<template>
    <AppLayout titulo="Ordens de Fornecimento">
    <Head title="Ordens de Fornecimento" />

    <!-- Cabeçalho premium -->
    <div class="rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #312e81 100%)">
      <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-white">Ordens de Fornecimento</h2>
          <p class="text-sm text-blue-300 mt-0.5">Aquisições, licitações e ordens operacionais da Defesa Civil</p>
        </div>
        <div class="flex items-center gap-2">
          <a
            :href="route('vendas.exportar')"
            class="flex items-center gap-2 text-sm font-medium text-white/70 border border-white/25 rounded-xl px-4 py-2 hover:bg-white/10 transition-all"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar CSV
          </a>
          <Botao :href="route('vendas.create')" class="!bg-white !text-slate-800 hover:!bg-slate-100 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Ordem
          </Botao>
        </div>
      </div>
    </div>

    <!-- Cards de Resumo com gradiente -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-pink-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-pink-600 bg-pink-100 rounded-full px-2 py-0.5">hoje</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.total_hoje }}</p>
        <p class="text-xs text-gray-500 mt-1">Ordens Hoje</p>
      </div>

      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-emerald-600 bg-emerald-100 rounded-full px-2 py-0.5">hoje</span>
        </div>
        <p class="text-xl font-bold text-gray-900">{{ resumo.receita_hoje }}</p>
        <p class="text-xs text-gray-500 mt-1">Valor Hoje</p>
      </div>

      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-amber-400 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-amber-600 bg-amber-100 rounded-full px-2 py-0.5">aguardando</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.pendentes }}</p>
        <p class="text-xs text-gray-500 mt-1">Pendentes</p>
      </div>

      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-rose-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-rose-600 bg-rose-100 rounded-full px-2 py-0.5">este mês</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.canceladas }}</p>
        <p class="text-xs text-gray-500 mt-1">Canceladas</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
      <p class="text-sm font-semibold text-gray-700 mb-3">Filtrar ordens</p>
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
          </svg>
          <input v-model="filtroForm.busca" type="search" placeholder="Buscar por número ou entidade..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 bg-gray-50" />
        </div>
        <select v-model="filtroForm.status" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
          <option value="">Todos status</option>
          <option value="pendente">Pendente</option>
          <option value="processando">Processando</option>
          <option value="concluido">Concluído</option>
          <option value="cancelado">Cancelado</option>
        </select>
        <input v-model="filtroForm.data_inicio" type="date" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50" />
        <input v-model="filtroForm.data_fim" type="date" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50" />
        <Botao type="submit" size="md">Filtrar</Botao>
      </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
          <h3 class="font-bold text-gray-900">Lista de Ordens</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ vendas.total ?? 0 }} registros encontrados</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3.5 uppercase tracking-wide">Nº Pedido</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Entidade</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden lg:table-cell">Responsável</th>
              <th class="text-right text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Total</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden md:table-cell">Pagamento</th>
              <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Status</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden sm:table-cell">Data</th>
              <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="venda in vendas.data" :key="venda.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <span class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">{{ venda.numero_pedido }}</span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ venda.cliente?.[0]?.toUpperCase() ?? 'A' }}
                  </div>
                  <span class="text-sm font-medium text-gray-800">{{ venda.cliente }}</span>
                </div>
              </td>
              <td class="px-4 py-4 text-sm text-gray-500 hidden lg:table-cell">{{ venda.vendedor }}</td>
              <td class="px-4 py-4 text-sm font-bold text-gray-900 text-right">R$ {{ venda.total }}</td>
              <td class="px-4 py-4 text-sm text-gray-500 hidden md:table-cell">
                <span class="inline-flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                  {{ venda.forma_pagamento }}
                </span>
              </td>
              <td class="px-4 py-4 text-center">
                <Badge :variant="badgeStatus(venda.status)">{{ venda.status_label }}</Badge>
              </td>
              <td class="px-4 py-4 text-sm text-gray-500 hidden sm:table-cell">{{ venda.data }}</td>
              <td class="px-4 py-4">
                <div class="flex items-center justify-center gap-1.5">
                  <Link :href="route('vendas.show', venda.id)" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ver detalhes">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </Link>
                  <Link :href="route('vendas.edit', venda.id)" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Editar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </Link>
                  <button
                    v-if="venda.status !== 'cancelado'"
                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    @click="confirmarCancelamento(venda)"
                    title="Cancelar"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!vendas.data.length">
              <td colspan="8" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                  </div>
                  <p class="text-sm font-medium text-gray-400">Nenhuma ordem encontrada</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Paginacao
        :links="vendas.links"
        :de="vendas.from"
        :ate="vendas.to"
        :total="vendas.total"
        class="px-6 py-4 border-t border-gray-100"
      />
    </div>

    <!-- Modal Cancelamento -->
    <Modal :aberto="!!vendaCancelar" titulo="Cancelar Ordem" @fechar="vendaCancelar = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja cancelar a ordem
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

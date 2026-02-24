<template>
  <AppLayout titulo="Entidades">
    <Head title="Entidades" />

    <!-- Cabeçalho premium -->
    <div class="rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 60%, #6d28d9 100%)">
      <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-white">Entidades Atendidas</h2>
          <p class="text-sm text-indigo-200 mt-0.5">Municípios, órgãos públicos e instituições parceiras da Defesa Civil</p>
        </div>
        <Botao :href="route('clientes.create')" class="!bg-white !text-indigo-700 hover:!bg-indigo-50 shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Nova Entidade
        </Botao>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
      <p class="text-sm font-semibold text-gray-700 mb-3">Filtrar entidades</p>
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input v-model="filtroForm.busca" type="search" placeholder="Buscar por nome, e-mail ou CNPJ..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50"/>
        </div>
        <select v-model="filtroForm.tipo" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
          <option value="">Todos tipos</option>
          <option value="pessoa_fisica">Órgão Público</option>
          <option value="pessoa_juridica">Empresa / Inst. Parceira</option>
        </select>
        <Botao type="submit" size="md">Filtrar</Botao>
        <Botao variant="secundario" size="md" type="button" @click="limparFiltros">Limpar</Botao>
      </form>
    </div>

    <!-- Cards resumo com gradientes -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.total }}</p>
        <p class="text-xs text-gray-500 mt-1">Total de Entidades</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.pf }}</p>
        <p class="text-xs text-gray-500 mt-1">Órgãos Públicos</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #faf5ff 0%, #ede9fe 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.pj }}</p>
        <p class="text-xs text-gray-500 mt-1">Empresas Parceiras</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.com_compras }}</p>
        <p class="text-xs text-gray-500 mt-1">Com Ordens</p>
      </div>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
          <h3 class="font-bold text-gray-900">Registro de Entidades</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ clientes.total ?? 0 }} registros encontrados</p>
        </div>
      </div>
      <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3.5 uppercase tracking-wide">Entidade</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden md:table-cell">CNPJ / CPF</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Tipo</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden sm:table-cell">Ordens</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide hidden lg:table-cell">Cidade / UF</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="cliente in clientes.data" :key="cliente.id" class="hover:bg-indigo-50/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-sm" :style="{ background: 'linear-gradient(135deg, ' + clienteColor(cliente.nome) + ')' }">
                  {{ cliente.nome?.[0]?.toUpperCase() ?? 'C' }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ cliente.nome }}</p>
                  <p class="text-xs text-gray-400">{{ cliente.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-4 text-sm text-gray-600 hidden md:table-cell">
              <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ cliente.cpf_cnpj || '—' }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <Badge :variant="cliente.tipo === 'pessoa_fisica' ? 'azul' : 'roxo'">{{ cliente.tipo === 'pessoa_fisica' ? 'Órgão Público' : 'Parceiro' }}</Badge>
            </td>
            <td class="px-4 py-4 text-center hidden sm:table-cell">
              <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ cliente.vendas_count }}</span>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500 hidden lg:table-cell">
              <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ cliente.cidade_uf || '—' }}
              </span>
            </td>
            <td class="px-4 py-4">
              <div class="flex items-center justify-center gap-2">
                <Link :href="route('clientes.show', cliente.id)" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ver detalhes">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
                <Link :href="route('clientes.edit', cliente.id)" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" @click="confirmarExclusao(cliente)" title="Excluir">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!clientes.data.length">
            <td colspan="6" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Nenhuma entidade encontrada</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
      <Paginacao :links="clientes.links" :de="clientes.from" :ate="clientes.to" :total="clientes.total" class="px-6 py-4 border-t border-gray-100" />
    </div>

    <!-- Modal exclusão -->
    <Modal :aberto="!!clienteExcluir" titulo="Excluir Entidade" @fechar="clienteExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja excluir a entidade <strong>{{ clienteExcluir?.nome }}</strong>?
        <span v-if="clienteExcluir?.vendas_count > 0" class="block mt-2 text-amber-600">
          Atenção: esta entidade possui {{ clienteExcluir.vendas_count }} ordem(ns) registrada(s).
        </span>
      </p>
      <template #footer>
        <Botao variant="secundario" @click="clienteExcluir = null">Cancelar</Botao>
        <Botao variant="perigo" :carregando="excluindo" @click="excluir">Excluir</Botao>
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
  clientes: { type: Object, required: true },
  resumo:   { type: Object, default: () => ({ total: 0, pf: 0, pj: 0, com_compras: 0 }) },
  filtros:  { type: Object, default: () => ({}) },
});

const clienteColor = (nome) => {
  const cores = [
    '#6366f1, #8b5cf6', '#ec4899, #f43f5e', '#10b981, #059669',
    '#f59e0b, #d97706', '#3b82f6, #2563eb', '#8b5cf6, #6d28d9',
  ];
  const idx = (nome?.charCodeAt(0) ?? 0) % cores.length;
  return cores[idx];
};

const filtroForm = ref({ busca: props.filtros.busca ?? '', tipo: props.filtros.tipo ?? '' });
const filtrar    = () => router.get(route('clientes.index'), filtroForm.value, { preserveState: true });
const limparFiltros = () => { filtroForm.value = { busca: '', tipo: '' }; filtrar(); };

const clienteExcluir = ref(null);
const excluindo      = ref(false);
const confirmarExclusao = (c) => { clienteExcluir.value = c; };
const excluir = () => {
  excluindo.value = true;
  router.delete(route('clientes.destroy', clienteExcluir.value.id), {
    onFinish: () => { excluindo.value = false; clienteExcluir.value = null; },
  });
};
</script>

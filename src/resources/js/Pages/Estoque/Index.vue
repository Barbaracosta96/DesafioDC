<template>
    <AppLayout titulo="Ativos Tecnológicos">
    <Head title="Ativos Tecnológicos" />

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Gestão de Ativos Tecnológicos</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie equipamentos e ativos operacionais da Defesa Civil</p>
      </div>
      <Botao :href="route('estoque.create')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Adicionar Ativo
      </Botao>
    </div>

    <!-- Cards de Resumo com gradientes -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 rounded-full px-2 py-0.5">ativo</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.total }}</p>
        <p class="text-xs text-gray-500 mt-1">Ativos Operacionais</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-amber-400 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-amber-600 bg-amber-100 rounded-full px-2 py-0.5">alerta</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.estoque_baixo }}</p>
        <p class="text-xs text-gray-500 mt-1">Estoque Baixo</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gray-400 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
          <span class="text-xs font-semibold text-gray-500 bg-gray-200 rounded-full px-2 py-0.5">inativo</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ resumo.inativos }}</p>
        <p class="text-xs text-gray-500 mt-1">Inativos</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
      <p class="text-sm font-semibold text-gray-700 mb-3">Filtrar ativos</p>
      <form @submit.prevent="filtrar" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
          </svg>
          <input v-model="filtroForm.busca" type="search" placeholder="Buscar por equipamento ou SKU..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50" />
        </div>
        <select v-model="filtroForm.categoria_id" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
          <option value="">Todas categorias</option>
          <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nome }}</option>
        </select>
        <select v-model="filtroForm.status" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
          <option value="">Todos status</option>
          <option value="ativo">Ativo</option>
          <option value="inativo">Inativo</option>
          <option value="baixo">Estoque baixo</option>
        </select>
        <Botao type="submit" size="md">Filtrar</Botao>
      </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
          <h3 class="font-bold text-gray-900">Inventário de Ativos</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ produtos.total ?? 0 }} registros encontrados</p>
        </div>
      </div>
      <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3.5 uppercase tracking-wide">Equipamento / Ativo</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">SKU</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Categoria</th>
            <th class="text-right text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Preço</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Estoque</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Status</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="produto in produtos.data" :key="produto.id" class="hover:bg-indigo-50/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ produto.nome }}</p>
              </div>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500">
              <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ produto.codigo_sku ?? '—' }}</span>
            </td>
            <td class="px-4 py-4">
              <span class="text-xs font-medium text-gray-600 bg-gray-100 rounded-full px-2.5 py-1">{{ produto.categoria?.nome ?? '—' }}</span>
            </td>
            <td class="px-4 py-4 text-sm font-bold text-gray-900 text-right">
              R$ {{ Number(produto.preco_venda).toFixed(2).replace('.', ',') }}
            </td>
            <td class="px-4 py-4 text-center">
              <div class="flex flex-col items-center gap-1">
                <span :class="['text-sm font-bold', produto.quantidade_estoque <= produto.estoque_minimo ? 'text-red-600' : 'text-emerald-600']">{{ produto.quantidade_estoque }}</span>
                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div :style="{ width: Math.min(100, (produto.quantidade_estoque / Math.max(produto.estoque_minimo * 3, 1)) * 100) + '%' }" :class="['h-full rounded-full', produto.quantidade_estoque <= produto.estoque_minimo ? 'bg-red-400' : 'bg-emerald-400']"></div>
                </div>
                <span class="text-xs text-gray-400">mín {{ produto.estoque_minimo }}</span>
              </div>
            </td>
            <td class="px-4 py-4 text-center">
              <Badge :variant="produto.ativo ? 'verde' : 'cinza'">
                {{ produto.ativo ? 'Ativo' : 'Inativo' }}
              </Badge>
            </td>
            <td class="px-4 py-4">
              <div class="flex items-center justify-center gap-2">
                <Link :href="route('estoque.edit', produto.id)" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button
                  class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  @click="confirmarExclusao(produto)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!produtos.data.length">
            <td colspan="7" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Nenhum ativo encontrado</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
      <Paginacao :links="produtos.links" :de="produtos.from" :ate="produtos.to" :total="produtos.total" class="px-6 py-4 border-t border-gray-100" />
    </div>

    <!-- Modal de confirmação de exclusão -->
    <Modal :aberto="!!produtoExcluir" titulo="Remover ativo" @fechar="produtoExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja remover o ativo
        <strong class="text-gray-900">{{ produtoExcluir?.nome }}</strong>?
        Esta ação não pode ser desfeita.
      </p>
      <template #footer>
        <Botao variant="secundario" @click="produtoExcluir = null">Cancelar</Botao>
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
  produtos:    { type: Object, required: true },
  categorias:  { type: Array,  default: () => [] },
  filtros:     { type: Object, default: () => ({}) },
  resumo:      { type: Object, default: () => ({}) },
});

const filtroForm = ref({ busca: props.filtros.busca ?? '', categoria_id: props.filtros.categoria_id ?? '', status: props.filtros.status ?? '' });
const filtrar = () => router.get(route('estoque.index'), filtroForm.value, { preserveState: true });

const produtoExcluir = ref(null);
const excluindo      = ref(false);

const confirmarExclusao = (produto) => { produtoExcluir.value = produto; };
const excluir = () => {
  excluindo.value = true;
  router.delete(route('estoque.destroy', produtoExcluir.value.id), {
    onFinish: () => { excluindo.value = false; produtoExcluir.value = null; },
  });
};
</script>

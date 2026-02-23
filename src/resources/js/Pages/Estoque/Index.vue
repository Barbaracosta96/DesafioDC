<template>
  <AppLayout titulo="Estoque">
    <Head title="Estoque" />

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Controle de Estoque</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie seus produtos e inventário</p>
      </div>
      <Botao :href="route('estoque.create')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Adicionar Produto
      </Botao>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Ativo</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ resumo.total }}</p>
      </div>
      <div class="bg-white rounded-xl border border-yellow-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Estoque Baixo</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ resumo.estoque_baixo }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Inativos</p>
        <p class="text-2xl font-bold text-gray-400 mt-1">{{ resumo.inativos }}</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
      <form @submit.prevent="filtrar" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
          <input
            v-model="filtroForm.busca"
            type="search"
            placeholder="Buscar por nome ou SKU..."
            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
        </div>
        <select
          v-model="filtroForm.categoria_id"
          class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <option value="">Todas categorias</option>
          <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nome }}</option>
        </select>
        <select
          v-model="filtroForm.status"
          class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
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
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Produto</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">SKU</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Categoria</th>
            <th class="text-right text-xs font-medium text-gray-500 px-4 py-3 uppercase">Preço Venda</th>
            <th class="text-right text-xs font-medium text-gray-500 px-4 py-3 uppercase">Estoque</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Status</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="produto in produtos.data" :key="produto.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
              <p class="text-sm font-medium text-gray-900">{{ produto.nome }}</p>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ produto.codigo_sku ?? '—' }}</td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ produto.categoria?.nome ?? '—' }}</td>
            <td class="px-4 py-4 text-sm font-medium text-gray-900 text-right">
              R$ {{ Number(produto.preco_venda).toFixed(2).replace('.', ',') }}
            </td>
            <td class="px-4 py-4 text-right">
              <span :class="['text-sm font-semibold', produto.quantidade_estoque <= produto.estoque_minimo ? 'text-red-600' : 'text-gray-900']">
                {{ produto.quantidade_estoque }}
              </span>
              <span class="text-xs text-gray-400 ml-1">/ mín {{ produto.estoque_minimo }}</span>
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
            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
              Nenhum produto encontrado.
            </td>
          </tr>
        </tbody>
      </table>

      <Paginacao
        :links="produtos.links"
        :de="produtos.from"
        :ate="produtos.to"
        :total="produtos.total"
        class="px-6 py-4 border-t border-gray-100"
      />
    </div>

    <!-- Modal de confirmação de exclusão -->
    <Modal :aberto="!!produtoExcluir" titulo="Excluir produto" @fechar="produtoExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja excluir o produto
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

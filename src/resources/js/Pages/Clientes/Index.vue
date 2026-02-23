<template>
  <AppLayout titulo="Clientes">
    <Head title="Clientes" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Clientes</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie sua base de clientes</p>
      </div>
      <Botao :href="route('clientes.create')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Novo Cliente
      </Botao>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <input v-model="filtroForm.busca" type="search" placeholder="Buscar por nome, e-mail ou CPF/CNPJ..." class="flex-1 min-w-48 px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <select v-model="filtroForm.tipo" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="">Todos tipos</option>
          <option value="pessoa_fisica">Pessoa Física</option>
          <option value="pessoa_juridica">Pessoa Jurídica</option>
        </select>
        <Botao type="submit" size="md">Filtrar</Botao>
        <Botao variant="secundario" size="md" type="button" @click="limparFiltros">Limpar</Botao>
      </form>
    </div>

    <!-- Cards resumo -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Total</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ resumo.total }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Pessoa Física</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ resumo.pf }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Pessoa Jurídica</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">{{ resumo.pj }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Com Compras</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ resumo.com_compras }}</p>
      </div>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Cliente</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase hidden md:table-cell">CPF/CNPJ</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Tipo</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase hidden sm:table-cell">Compras</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase hidden lg:table-cell">Cidade / UF</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="cliente in clientes.data" :key="cliente.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
              <p class="text-sm font-medium text-gray-900">{{ cliente.nome }}</p>
              <p class="text-xs text-gray-400">{{ cliente.email }}</p>
            </td>
            <td class="px-4 py-4 text-sm text-gray-600 hidden md:table-cell">{{ cliente.cpf_cnpj || '—' }}</td>
            <td class="px-4 py-4 text-center">
              <Badge :variant="cliente.tipo === 'pessoa_fisica' ? 'azul' : 'roxo'">{{ cliente.tipo === 'pessoa_fisica' ? 'PF' : 'PJ' }}</Badge>
            </td>
            <td class="px-4 py-4 text-center text-sm font-medium text-gray-700 hidden sm:table-cell">{{ cliente.vendas_count }}</td>
            <td class="px-4 py-4 text-sm text-gray-500 hidden lg:table-cell">{{ cliente.cidade_uf || '—' }}</td>
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
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum cliente encontrado.</td>
          </tr>
        </tbody>
      </table>
      <Paginacao :links="clientes.links" :de="clientes.from" :ate="clientes.to" :total="clientes.total" class="px-6 py-4 border-t border-gray-100" />
    </div>

    <!-- Modal exclusão -->
    <Modal :aberto="!!clienteExcluir" titulo="Excluir Cliente" @fechar="clienteExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja excluir o cliente <strong>{{ clienteExcluir?.nome }}</strong>?
        <span v-if="clienteExcluir?.vendas_count > 0" class="block mt-2 text-amber-600">
          Atenção: este cliente possui {{ clienteExcluir.vendas_count }} venda(s) registrada(s).
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

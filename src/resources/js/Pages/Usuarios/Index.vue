<template>
  <AppLayout titulo="Usuários">
    <Head title="Usuários" />

    <!-- Cabeçalho premium -->
    <div class="rounded-2xl mb-6 overflow-hidden" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #6d28d9 100%)">
      <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-white">Controle de Acessos</h2>
          <p class="text-sm text-indigo-200 mt-0.5">Gerencie os operadores e perfis de acesso ao sistema</p>
        </div>
        <Botao :href="route('usuarios.create')" class="!bg-white !text-indigo-700 hover:!bg-indigo-50 shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Novo Operador
        </Botao>
      </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 rounded-full px-2 py-0.5">total</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ usuarios.total ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Operadores Cadastrados</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #faf5ff 0%, #ede9fe 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-violet-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-violet-600 bg-violet-100 rounded-full px-2 py-0.5">admin</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ roleCount('admin') }}</p>
        <p class="text-xs text-gray-500 mt-1">Administradores</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-blue-600 bg-blue-100 rounded-full px-2 py-0.5">editor</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ roleCount('editor') }}</p>
        <p class="text-xs text-gray-500 mt-1">Editores</p>
      </div>
      <div class="rounded-2xl p-5 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-emerald-600 bg-emerald-100 rounded-full px-2 py-0.5">usuário</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ roleCount('usuario') }}</p>
        <p class="text-xs text-gray-500 mt-1">Operadores Padrão</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
      <p class="text-sm font-semibold text-gray-700 mb-3">Filtrar usuários</p>
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
          </svg>
          <input v-model="filtroForm.busca" type="search" placeholder="Buscar por nome ou e-mail..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50" />
        </div>
        <select v-model="filtroForm.role" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
          <option value="">Todos perfis</option>
          <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
        </select>
        <Botao type="submit" size="md">Filtrar</Botao>
      </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
          <h3 class="font-bold text-gray-900">Lista de Usuários</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ usuarios.total ?? 0 }} registros encontrados</p>
        </div>
      </div>
      <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3.5 uppercase tracking-wide">Usuário</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">E-mail</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Perfis</th>
            <th class="text-left text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Cadastrado em</th>
            <th class="text-center text-xs font-semibold text-gray-500 px-4 py-3.5 uppercase tracking-wide">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="usuario in usuarios.data" :key="usuario.id" class="hover:bg-indigo-50/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-sm" :style="{ background: 'linear-gradient(135deg, ' + userColor(usuario.name) + ')' }">
                  {{ iniciais(usuario.name) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ usuario.name }}</p>
                  <p class="text-xs text-gray-400">{{ usuario.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-4 text-sm text-gray-600">{{ usuario.email }}</td>
            <td class="px-4 py-4 text-center">
              <div class="flex flex-wrap gap-1 justify-center">
                <Badge v-for="r in usuario.roles" :key="r" :variant="badgeRole(r)">{{ r }}</Badge>
              </div>
            </td>
            <td class="px-4 py-4 text-sm text-gray-500">{{ usuario.criado }}</td>
            <td class="px-4 py-4">
              <div class="flex items-center justify-center gap-2">
                <Link :href="route('usuarios.edit', usuario.id)" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" @click="confirmarExclusao(usuario)">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!usuarios.data.length">
            <td colspan="5" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Nenhum usuário encontrado</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
      <Paginacao :links="usuarios.links" :de="usuarios.from" :ate="usuarios.to" :total="usuarios.total" class="px-6 py-4 border-t border-gray-100" />
    </div>

    <Modal :aberto="!!usuarioExcluir" titulo="Excluir Usuário" @fechar="usuarioExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja excluir o usuário <strong>{{ usuarioExcluir?.name }}</strong>?
      </p>
      <template #footer>
        <Botao variant="secundario" @click="usuarioExcluir = null">Cancelar</Botao>
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
  usuarios: { type: Object, required: true },
  roles:    { type: Array,  default: () => [] },
  filtros:  { type: Object, default: () => ({}) },
});

const filtroForm = ref({ busca: props.filtros.busca ?? '', role: props.filtros.role ?? '' });
const filtrar    = () => router.get(route('usuarios.index'), filtroForm.value, { preserveState: true });

const usuarioExcluir = ref(null);
const excluindo      = ref(false);
const confirmarExclusao = (u) => { usuarioExcluir.value = u; };
const excluir = () => {
  excluindo.value = true;
  router.delete(route('usuarios.destroy', usuarioExcluir.value.id), {
    onFinish: () => { excluindo.value = false; usuarioExcluir.value = null; },
  });
};

const iniciais   = (nome) => nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
const badgeRole  = (r) => ({ admin: 'roxo', editor: 'azul', usuario: 'cinza' }[r] ?? 'cinza');
const roleCount  = (role) => props.usuarios?.data?.filter(u => u.roles?.includes(role)).length ?? 0;
const userColor  = (nome) => {
  const cores = [
    '#6366f1, #8b5cf6', '#ec4899, #f43f5e', '#10b981, #059669',
    '#f59e0b, #d97706', '#3b82f6, #2563eb', '#8b5cf6, #6d28d9',
  ];
  const idx = (nome?.charCodeAt(0) ?? 0) % cores.length;
  return cores[idx];
};
</script>

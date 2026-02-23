<template>
  <AppLayout titulo="Usuários">
    <Head title="Usuários" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Gestão de Usuários</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie os acessos da plataforma</p>
      </div>
      <Botao :href="route('usuarios.create')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Novo Usuário
      </Botao>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
      <form @submit.prevent="filtrar" class="flex flex-wrap gap-3">
        <input v-model="filtroForm.busca" type="search" placeholder="Buscar por nome ou e-mail..." class="flex-1 min-w-48 px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <select v-model="filtroForm.role" class="px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <option value="">Todos perfis</option>
          <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
        </select>
        <Botao type="submit" size="md">Filtrar</Botao>
      </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Usuário</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">E-mail</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Perfis</th>
            <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Cadastrado em</th>
            <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="usuario in usuarios.data" :key="usuario.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-semibold">
                  {{ iniciais(usuario.name) }}
                </div>
                <p class="text-sm font-medium text-gray-900">{{ usuario.name }}</p>
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
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum usuário encontrado.</td>
          </tr>
        </tbody>
      </table>
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
</script>
